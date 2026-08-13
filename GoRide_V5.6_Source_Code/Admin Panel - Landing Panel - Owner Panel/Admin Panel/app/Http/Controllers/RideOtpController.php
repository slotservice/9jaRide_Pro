<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;

class RideOtpController extends Controller
{
    // Same fallback as AuthTokenController, so this keeps working before
    // TERMII_API_KEY is added to the VPS .env. Prefer config().
    private const TERMII_KEY_FALLBACK = 'TLQWUgFmliowHdqTGgUDjIRhIkIgVDHIEexBOIfHpIZkZOKQBrXEGuGGtFeFMs';

    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Text the rider the code they already have on screen, for the times they
     * cannot get to it.
     *
     * This lives on the server rather than in the app on purpose. Every send
     * costs real money, so the caller has to prove they are signed in as the
     * rider who booked this ride before anything is sent, and the code itself
     * is read from the order rather than accepted from the request. Doing it
     * here also keeps the Termii key off the handset.
     */
    public function sendOtpSms(Request $request)
    {
        $orderId = trim((string) $request->input('orderId', ''));
        $idToken = trim((string) $request->input('idToken', ''));

        if ($orderId === '' || $idToken === '') {
            return response()->json(['error' => 'Order and sign in token are required'], 400);
        }

        try {
            $verified = $this->auth->verifyIdToken($idToken);
            $uid = (string) $verified->claims()->get('sub');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Not signed in'], 401);
        }

        if ($uid === '') {
            return response()->json(['error' => 'Not signed in'], 401);
        }

        try {
            $db = app(Firestore::class)->database();

            $snapshot = $db->collection('orders')->document($orderId)->snapshot();
            if (!$snapshot->exists()) {
                return response()->json(['error' => 'Ride not found'], 404);
            }
            $order = $snapshot->data();

            // The signed in rider must own this ride. Without this anyone with
            // a ride id could burn the SMS balance.
            if ((string) ($order['userId'] ?? '') !== $uid) {
                return response()->json(['error' => 'Not your ride'], 403);
            }

            $status = (string) ($order['status'] ?? '');
            if (!in_array($status, ['Ride Placed', 'Ride Active'], true)) {
                return response()->json(['error' => 'The code is only needed before the trip starts'], 409);
            }

            $otp = (string) ($order['otp'] ?? '');
            if ($otp === '') {
                return response()->json(['error' => 'This ride has no code'], 409);
            }

            $phone = $this->riderPhone($db, $uid, $order);
            if ($phone === '') {
                return response()->json(['error' => 'No phone number on this account'], 409);
            }

            $termiiKey  = config('services.termii.key') ?: self::TERMII_KEY_FALLBACK;
            $termiiBase = rtrim(config('services.termii.base_url') ?: 'https://v3.api.termii.com', '/');

            $response = Http::asJson()->timeout(20)->post($termiiBase . '/api/sms/send', [
                'api_key' => $termiiKey,
                'to'      => $phone,
                'from'    => 'N-Alert',
                'sms'     => 'Your 9jaRide Pro ride code is ' . $otp . '. Give it to your driver to start the trip.',
                'type'    => 'plain',
                'channel' => 'dnd',
            ]);

            // Termii answers 200 with a message_id on success. Anything else is
            // a failure, including a 200 carrying an error payload.
            $messageId = $response->json('message_id');
            if (!$response->successful() || empty($messageId)) {
                Log::warning('Ride code SMS refused by Termii', [
                    'order'  => $orderId,
                    'status' => $response->status(),
                    'body'   => mb_substr($response->body(), 0, 500),
                ]);
                return response()->json(['error' => 'Could not send the text right now'], 502);
            }

            return response()->json(['sent' => true]);
        } catch (\Throwable $e) {
            Log::error('Ride code SMS error', ['order' => $orderId, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Could not send the text right now'], 500);
        }
    }

    /**
     * Where to send it. On a ride booked for somebody else the passenger is a
     * different person, and they are the one who has to read the code out, so
     * their contact number wins over the account holder's.
     */
    private function riderPhone($db, string $uid, array $order): string
    {
        $someOneElse = $order['someOneElse'] ?? null;
        if (is_array($someOneElse) && !empty($someOneElse['contactNumber'])) {
            return $this->normalisePhone((string) $someOneElse['contactNumber']);
        }

        $user = $db->collection('users')->document($uid)->snapshot();
        if ($user->exists()) {
            $data = $user->data();
            $number = (string) ($data['phoneNumber'] ?? '');
            if ($number !== '') {
                return $this->normalisePhone(((string) ($data['countryCode'] ?? '')) . $number);
            }
        }

        return '';
    }

    /**
     * Termii wants a bare international number, no plus sign and no spaces.
     * A local 0803... is assumed Nigerian and rewritten to 234803...
     */
    private function normalisePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (!is_string($digits) || $digits === '') {
            return '';
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '234' . substr($digits, 1);
        }
        return $digits;
    }
}
