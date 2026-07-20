<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Contract\Firestore;

/**
 * Automatic driver payouts to bank via Paystack Transfers.
 *
 * approve(): called from the admin Payout Request page. Verifies the driver's
 * bank account, creates a Paystack transfer recipient, and sends the money from
 * the business Paystack balance to the driver's bank. Idempotent per payout id.
 *
 * webhook(): Paystack calls this on transfer.success / transfer.failed /
 * transfer.reversed to finalise the payout; on failure the amount is refunded
 * to the driver wallet.
 *
 * Prerequisites on the Paystack dashboard (business side, not code):
 *  - Transfers enabled, "OTP for transfers" DISABLED (for hands-off payouts).
 *  - A funded Paystack balance (payouts are sent from the balance).
 */
class PayoutTransferController extends Controller
{
    private const PAYSTACK = 'https://api.paystack.co';

    private function db()
    {
        return app(Firestore::class)->database();
    }

    private function paystackSecret(): ?string
    {
        try {
            $snap = $this->db()->collection('settings')->document('payment')->snapshot();
            $data = $snap->exists() ? $snap->data() : [];
            $ps = $data['payStack'] ?? [];
            $key = $ps['secretKey'] ?? null;
            return (is_string($key) && $key !== '') ? $key : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Admin "approve" button on the Payout Request page. Thin wrapper around
     * processPayout() so the manual and automatic paths run identical logic.
     */
    public function approve(Request $request)
    {
        $id = trim((string) $request->input('id', ''));
        if ($id === '') {
            return response()->json(['success' => false, 'message' => 'Missing payout id'], 400);
        }

        $r = $this->processPayout($id);

        if (($r['status'] ?? '') === 'otp') {
            return response()->json(['success' => false, 'otp' => true, 'message' => $r['message']], 200);
        }
        return response()->json(
            ['success' => (bool) ($r['ok'] ?? false), 'message' => $r['message'] ?? ''],
            (int) ($r['httpCode'] ?? (($r['ok'] ?? false) ? 200 : 422))
        );
    }

    /**
     * Core payout logic — verify the driver's bank, create a Paystack recipient,
     * and transfer the money from the business balance. Used by both the admin
     * approve button and the automatic payout job.
     *
     * Returns an array: ok(bool), status('sent'|'otp'|'error'), httpCode(int),
     * permanent(bool — a permanent failure should NOT be auto-retried), message.
     * Double-payment is impossible: only a 'pending' request is ever processed,
     * and it is flipped to 'approved' the moment the transfer is accepted.
     */
    public function processPayout(string $id): array
    {
        $secret = $this->paystackSecret();
        if (!$secret) {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 500, 'permanent' => true, 'message' => 'Paystack secret key is not configured in Settings.'];
        }

        $db = $this->db();
        $ref = $db->collection('withdrawal_history')->document($id);
        $snap = $ref->snapshot();
        if (!$snap->exists()) {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 404, 'permanent' => true, 'message' => 'Payout request not found.'];
        }
        $w = $snap->data();

        // Idempotency — only a pending request can be paid out.
        if (($w['paymentStatus'] ?? '') !== 'pending') {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 409, 'permanent' => true, 'message' => 'This payout is already ' . ($w['paymentStatus'] ?? 'processed') . '.'];
        }

        $userId = (string) ($w['userId'] ?? '');
        $amountNaira = floatval($w['amount'] ?? 0);
        if ($amountNaira <= 0) {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 400, 'permanent' => true, 'message' => 'Invalid payout amount.'];
        }
        $amountKobo = (int) round($amountNaira * 100);

        // Driver bank details.
        $bank = null;
        foreach ($db->collection('bank_details')->where('userId', '=', $userId)->limit(1)->documents() as $d) {
            if ($d->exists()) {
                $bank = $d->data();
            }
        }
        if (!$bank) {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => true, 'message' => 'Driver has not added bank details yet.'];
        }

        $accountNumber = trim((string) ($bank['accountNumber'] ?? ''));
        $bankCode = trim((string) ($bank['bankCode'] ?? ''));
        $bankName = trim((string) ($bank['bankName'] ?? ''));
        if ($accountNumber === '') {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => true, 'message' => 'Driver bank account number is missing.'];
        }

        // Resolve bank code from the stored bank name if the app has not saved a
        // proper code yet (older bank_details). Best-effort.
        if ($bankCode === '') {
            $bankCode = $this->resolveBankCode($secret, $bankName);
            if ($bankCode === '') {
                return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => true, 'message' => 'Could not match the driver\'s bank. Ask the driver to re-select their bank in the app.'];
            }
        }

        // Verify the account exists and get the real account name before sending.
        $resolve = Http::withToken($secret)->timeout(30)->get(self::PAYSTACK . '/bank/resolve', [
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ]);
        if (!$resolve->successful() || !$resolve->json('status')) {
            return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => true, 'message' => 'Could not verify the driver\'s bank account: ' . ($resolve->json('message') ?? 'invalid account')];
        }
        $verifiedName = (string) ($resolve->json('data.account_name') ?? ($bank['holderName'] ?? 'Driver'));

        // 1) Transfer recipient.
        $recipientRes = Http::withToken($secret)->asJson()->timeout(30)->post(self::PAYSTACK . '/transferrecipient', [
            'type' => 'nuban',
            'name' => $verifiedName,
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
            'currency' => 'NGN',
        ]);
        if (!$recipientRes->successful() || !$recipientRes->json('status')) {
            // Network/transient with Paystack — safe to retry later.
            return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => false, 'message' => 'Paystack recipient error: ' . ($recipientRes->json('message') ?? 'unknown')];
        }
        $recipientCode = (string) $recipientRes->json('data.recipient_code');

        // 2) Transfer. The base reference is deterministic per payout so an
        // accidental double-click cannot pay twice. But a prior attempt that
        // did NOT succeed (e.g. OTP was still on) already consumed that
        // reference, and Paystack rejects a reused one ("duplicate_transfer_
        // reference"). So when retrying after a non-success attempt, use a
        // fresh reference. Double-payment stays impossible because only a
        // 'pending' request reaches here (the paymentStatus gate above).
        $reference = 'payout_' . $id;
        $priorRef = trim((string) ($w['transferReference'] ?? ''));
        $priorStatus = trim((string) ($w['transferStatus'] ?? ''));
        if ($priorRef !== '' && $priorStatus !== 'success') {
            $reference = 'payout_' . $id . '_' . substr(md5($priorRef . microtime(true)), 0, 8);
        }
        $transferRes = Http::withToken($secret)->asJson()->timeout(40)->post(self::PAYSTACK . '/transfer', [
            'source' => 'balance',
            'amount' => $amountKobo,
            'recipient' => $recipientCode,
            'reason' => 'Driver payout',
            'reference' => $reference,
            'currency' => 'NGN',
        ]);

        if (!$transferRes->successful() || !$transferRes->json('status')) {
            $msg = (string) ($transferRes->json('message') ?? 'transfer failed');
            // Insufficient balance is transient — leave the request pending so it
            // retries automatically once the Paystack balance is funded again.
            $permanent = (stripos($msg, 'balance') === false);
            return ['ok' => false, 'status' => 'error', 'httpCode' => 422, 'permanent' => $permanent, 'message' => 'Paystack transfer error: ' . $msg];
        }

        $tData = $transferRes->json('data') ?? [];
        $tStatus = $tData['status'] ?? null;      // success | pending | otp | ...
        $tCode = $tData['transfer_code'] ?? null;

        // If OTP is still on for transfers, we cannot complete it automatically.
        if ($tStatus === 'otp') {
            $ref->set([
                'recipientCode' => $recipientCode,
                'transferCode' => $tCode,
                'transferReference' => $reference,
                'transferStatus' => 'otp',
            ], ['merge' => true]);
            return ['ok' => false, 'status' => 'otp', 'httpCode' => 200, 'permanent' => false, 'message' => 'Paystack is asking for a transfer OTP. In your Paystack dashboard, Settings > Preferences, turn OFF "OTP for transfers" so payouts complete automatically, then approve again.'];
        }

        // Mark approved now; the final paid/failed state is confirmed by the webhook.
        $ref->set([
            'paymentStatus' => 'approved',
            'recipientCode' => $recipientCode,
            'transferCode' => $tCode,
            'transferReference' => $reference,
            'transferStatus' => $tStatus ?: 'pending',
            'accountName' => $verifiedName,
            'paymentDate' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
        ], ['merge' => true]);

        return ['ok' => true, 'status' => 'sent', 'httpCode' => 200, 'permanent' => false, 'message' => 'Payout sent to ' . $verifiedName . '\'s bank. Status: ' . ($tStatus ?: 'processing') . '.'];
    }

    private function resolveBankCode(string $secret, string $bankName): string
    {
        if ($bankName === '') {
            return '';
        }
        try {
            $res = Http::withToken($secret)->timeout(30)->get(self::PAYSTACK . '/bank', ['country' => 'nigeria', 'perPage' => 200]);
            if (!$res->successful()) {
                return '';
            }
            $needle = strtolower(preg_replace('/[^a-z0-9]/i', '', $bankName));
            $banks = $res->json('data') ?? [];
            foreach ($banks as $b) {
                $name = strtolower(preg_replace('/[^a-z0-9]/i', '', $b['name'] ?? ''));
                if ($name === $needle) {
                    return (string) ($b['code'] ?? '');
                }
            }
            foreach ($banks as $b) {
                $name = strtolower(preg_replace('/[^a-z0-9]/i', '', $b['name'] ?? ''));
                if ($needle !== '' && $name !== '' && (strpos($name, $needle) !== false || strpos($needle, $name) !== false)) {
                    return (string) ($b['code'] ?? '');
                }
            }
        } catch (\Throwable $e) {
        }
        return '';
    }

    public function webhook(Request $request)
    {
        $secret = $this->paystackSecret();
        $signature = (string) $request->header('x-paystack-signature', '');
        $body = $request->getContent();
        if (!$secret || $signature === '' || !hash_equals(hash_hmac('sha512', $body, $secret), $signature)) {
            return response('invalid signature', 401);
        }

        $event = json_decode($body, true) ?: [];
        $type = $event['event'] ?? '';
        $data = $event['data'] ?? [];
        $reference = (string) ($data['reference'] ?? '');
        if (strpos($reference, 'payout_') !== 0) {
            return response('ignored', 200);
        }

        $db = $this->db();

        // Find the withdrawal by its stored transferReference. A retry carries a
        // suffix (payout_<id>_<hash>), so parsing the id out of the reference is
        // unsafe — match the stored field instead, with a legacy fallback to the
        // bare id for older payouts that used exactly payout_<id>.
        $ref = null;
        $w = null;
        foreach ($db->collection('withdrawal_history')->where('transferReference', '=', $reference)->limit(1)->documents() as $d) {
            if ($d->exists()) {
                $ref = $d->reference();
                $w = $d->data();
            }
        }
        if ($ref === null) {
            $legacyId = substr($reference, strlen('payout_'));
            $cand = $db->collection('withdrawal_history')->document($legacyId);
            $csnap = $cand->snapshot();
            if ($csnap->exists()) {
                $ref = $cand;
                $w = $csnap->data();
            }
        }
        if ($ref === null) {
            return response('not found', 200);
        }

        if ($type === 'transfer.success') {
            $ref->set(['paymentStatus' => 'approved', 'transferStatus' => 'success'], ['merge' => true]);
        } elseif ($type === 'transfer.failed' || $type === 'transfer.reversed') {
            // Refund once.
            if (($w['transferStatus'] ?? '') !== 'failed') {
                $userId = (string) ($w['userId'] ?? '');
                $amount = floatval($w['amount'] ?? 0);
                $driverRef = $db->collection('driver_users')->document($userId);
                $dsnap = $driverRef->snapshot();
                if ($dsnap->exists()) {
                    $current = floatval($dsnap->data()['walletAmount'] ?? 0);
                    // walletAmount is stored as a STRING in Firestore — keep it a string.
                    $driverRef->set(['walletAmount' => (string) ($current + $amount)], ['merge' => true]);
                }
                $ref->set([
                    'paymentStatus' => 'rejected',
                    'transferStatus' => 'failed',
                    'adminNote' => 'Bank transfer failed/reversed — amount refunded to wallet.',
                ], ['merge' => true]);
            }
        }

        return response('ok', 200);
    }
}
