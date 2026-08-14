<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Firestore;

/**
 * Credits wallet top ups that the app never hears about.
 *
 * A card top up completes inside the Paystack sheet while the rider is still on
 * the payment screen, so the app credits the wallet itself. A bank transfer does
 * not: Paystack marks it successful minutes later, long after the rider has
 * closed the screen, so nothing in the app ever runs and the money silently
 * fails to appear. Until now those were being added by hand.
 *
 * IMPORTANT, and the reason this is deliberately narrow: the in app top up
 * writes its wallet_transaction with a millisecond timestamp as transactionId,
 * NOT the Paystack reference. So there is no way from here to tell whether the
 * app already credited a given payment. If this webhook credited card payments
 * too, every card top up would be credited twice.
 *
 * It therefore only auto credits the channels that cannot have been credited in
 * the app. Everything else is logged and left alone. Under crediting is
 * recoverable by hand; over crediting is money out of the client's pocket.
 */
class PaystackChargeWebhookController extends Controller
{
    private const PAYSTACK = 'https://api.paystack.co';

    /**
     * Channels that settle after the payer has left the app, so the in app
     * credit could not have run. Card is deliberately absent.
     */
    private const CREDITABLE_CHANNELS = ['bank_transfer', 'dedicated_nuban'];

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

    public function webhook(Request $request)
    {
        $secret = $this->paystackSecret();
        $signature = (string) $request->header('x-paystack-signature', '');
        $body = $request->getContent();

        // Same check as the transfer webhook. Without it anyone could POST a
        // fake success and mint wallet balance.
        if (!$secret || $signature === '' || !hash_equals(hash_hmac('sha512', $body, $secret), $signature)) {
            return response('invalid signature', 401);
        }

        $event = json_decode($body, true) ?: [];
        if (($event['event'] ?? '') !== 'charge.success') {
            return response('ignored', 200);
        }

        $data = $event['data'] ?? [];
        $reference = (string) ($data['reference'] ?? '');
        if ($reference === '') {
            return response('ignored', 200);
        }

        try {
            // Never trust the posted body for the amount or the status. A valid
            // signature proves it came from Paystack, not that the body is the
            // whole story, so ask Paystack directly.
            $verify = Http::withToken($secret)->timeout(20)->get(self::PAYSTACK . '/transaction/verify/' . rawurlencode($reference));
            $tx = $verify->json('data') ?: [];

            if (!$verify->successful() || ($tx['status'] ?? '') !== 'success') {
                Log::info('Charge webhook: not a successful transaction', ['ref' => $reference, 'status' => $tx['status'] ?? null]);
                return response('ignored', 200);
            }

            $channel = (string) ($tx['channel'] ?? '');
            if (!in_array($channel, self::CREDITABLE_CHANNELS, true)) {
                // Card and friends are already credited by the app itself.
                Log::info('Charge webhook: channel handled in app, skipping', ['ref' => $reference, 'channel' => $channel]);
                return response('ignored', 200);
            }

            $kobo = (int) ($tx['amount'] ?? 0);
            if ($kobo <= 0) {
                return response('ignored', 200);
            }
            $amount = round($kobo / 100, 2);

            $email = strtolower(trim((string) (($tx['customer'] ?? [])['email'] ?? '')));
            if ($email === '') {
                Log::warning('Charge webhook: no email on transaction', ['ref' => $reference]);
                return response('ignored', 200);
            }

            $target = $this->findWallet($email);
            if ($target === null) {
                Log::warning('Charge webhook: no account for that email', ['ref' => $reference, 'email' => $email]);
                return response('ignored', 200);
            }

            $credited = $this->credit($target['collection'], $target['uid'], $target['userType'], $reference, $amount);

            Log::info('Charge webhook processed', [
                'ref'      => $reference,
                'email'    => $email,
                'uid'      => $target['uid'],
                'amount'   => $amount,
                'channel'  => $channel,
                'credited' => $credited,
            ]);

            return response('ok', 200);
        } catch (\Throwable $e) {
            Log::error('Charge webhook error', ['ref' => $reference, 'message' => $e->getMessage()]);
            // 500 so Paystack retries rather than treating a transient Firestore
            // problem as final and dropping the payment on the floor.
            return response('error', 500);
        }
    }

    /**
     * The app sends no metadata on the charge, so email is all we have to go on.
     * Riders come first because bank transfer top ups happen on the rider side;
     * driver_users is only a fallback for a driver only account.
     */
    private function findWallet(string $email): ?array
    {
        foreach ([['users', 'customer'], ['driver_users', 'driver']] as [$collection, $userType]) {
            foreach ($this->db()->collection($collection)->where('email', '=', $email)->limit(1)->documents() as $doc) {
                if ($doc->exists()) {
                    return ['collection' => $collection, 'uid' => $doc->id(), 'userType' => $userType];
                }
            }
        }
        return null;
    }

    /**
     * Credits the wallet once and once only. The ledger doc id is derived from
     * the Paystack reference, so a redelivered webhook finds it already there
     * and does nothing. walletAmount is a STRING throughout this database.
     */
    private function credit(string $collection, string $uid, string $userType, string $reference, float $amount): bool
    {
        $db = $this->db();
        $userRef = $db->collection($collection)->document($uid);
        $txRef = $db->collection('wallet_transaction')->document('reconcile-' . $reference);

        return $db->runTransaction(function ($transaction) use ($userRef, $txRef, $uid, $userType, $reference, $amount) {
            // Every read before any write. Firestore rejects the other order.
            $userSnap = $transaction->snapshot($userRef);
            $txSnap = $transaction->snapshot($txRef);

            if ($txSnap->exists()) {
                return false;
            }
            if (!$userSnap->exists()) {
                return false;
            }

            $before = (float) ($userSnap->data()['walletAmount'] ?? 0);
            $after = round($before + $amount, 2);

            $transaction->update($userRef, [['path' => 'walletAmount', 'value' => (string) $after]]);
            $transaction->set($txRef, [
                'id'            => 'reconcile-' . $reference,
                'transactionId' => $reference,
                'amount'        => (string) $amount,
                'userId'        => $uid,
                'paymentType'   => 'Paystack',
                'userType'      => $userType,
                'note'          => 'Wallet top up (bank transfer) reconciled',
                'createdDate'   => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                'orderType'     => 'city',
            ]);

            return true;
        });
    }
}
