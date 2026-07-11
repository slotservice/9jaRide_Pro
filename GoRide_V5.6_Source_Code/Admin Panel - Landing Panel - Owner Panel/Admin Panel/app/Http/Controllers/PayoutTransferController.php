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

    public function approve(Request $request)
    {
        $id = trim((string) $request->input('id', ''));
        if ($id === '') {
            return response()->json(['success' => false, 'message' => 'Missing payout id'], 400);
        }

        $secret = $this->paystackSecret();
        if (!$secret) {
            return response()->json(['success' => false, 'message' => 'Paystack secret key is not configured in Settings.'], 500);
        }

        $db = $this->db();
        $ref = $db->collection('withdrawal_history')->document($id);
        $snap = $ref->snapshot();
        if (!$snap->exists()) {
            return response()->json(['success' => false, 'message' => 'Payout request not found.'], 404);
        }
        $w = $snap->data();

        // Idempotency — only a pending request can be paid out.
        if (($w['paymentStatus'] ?? '') !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This payout is already ' . ($w['paymentStatus'] ?? 'processed') . '.'], 409);
        }

        $userId = (string) ($w['userId'] ?? '');
        $amountNaira = floatval($w['amount'] ?? 0);
        if ($amountNaira <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid payout amount.'], 400);
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
            return response()->json(['success' => false, 'message' => 'Driver has not added bank details yet.'], 422);
        }

        $accountNumber = trim((string) ($bank['accountNumber'] ?? ''));
        $bankCode = trim((string) ($bank['bankCode'] ?? ''));
        $bankName = trim((string) ($bank['bankName'] ?? ''));
        if ($accountNumber === '') {
            return response()->json(['success' => false, 'message' => 'Driver bank account number is missing.'], 422);
        }

        // Resolve bank code from the stored bank name if the app has not saved a
        // proper code yet (older bank_details). Best-effort.
        if ($bankCode === '') {
            $bankCode = $this->resolveBankCode($secret, $bankName);
            if ($bankCode === '') {
                return response()->json(['success' => false, 'message' => 'Could not match the driver\'s bank. Ask the driver to re-select their bank in the app.'], 422);
            }
        }

        // Verify the account exists and get the real account name before sending.
        $resolve = Http::withToken($secret)->timeout(30)->get(self::PAYSTACK . '/bank/resolve', [
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ]);
        if (!$resolve->successful() || !$resolve->json('status')) {
            return response()->json(['success' => false, 'message' => 'Could not verify the driver\'s bank account: ' . ($resolve->json('message') ?? 'invalid account')], 422);
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
            return response()->json(['success' => false, 'message' => 'Paystack recipient error: ' . ($recipientRes->json('message') ?? 'unknown')], 422);
        }
        $recipientCode = (string) $recipientRes->json('data.recipient_code');

        // 2) Transfer (idempotent by reference).
        $reference = 'payout_' . $id;
        $transferRes = Http::withToken($secret)->asJson()->timeout(40)->post(self::PAYSTACK . '/transfer', [
            'source' => 'balance',
            'amount' => $amountKobo,
            'recipient' => $recipientCode,
            'reason' => 'Driver payout',
            'reference' => $reference,
            'currency' => 'NGN',
        ]);

        if (!$transferRes->successful() || !$transferRes->json('status')) {
            return response()->json(['success' => false, 'message' => 'Paystack transfer error: ' . ($transferRes->json('message') ?? 'transfer failed')], 422);
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
            return response()->json([
                'success' => false,
                'otp' => true,
                'message' => 'Paystack is asking for a transfer OTP. In your Paystack dashboard, Settings > Preferences, turn OFF "OTP for transfers" so payouts complete automatically, then approve again.',
            ], 200);
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

        return response()->json(['success' => true, 'message' => 'Payout sent to ' . $verifiedName . '\'s bank. Status: ' . ($tStatus ?: 'processing') . '.']);
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
        $id = substr($reference, strlen('payout_'));

        $db = $this->db();
        $ref = $db->collection('withdrawal_history')->document($id);
        $snap = $ref->snapshot();
        if (!$snap->exists()) {
            return response('not found', 200);
        }
        $w = $snap->data();

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
