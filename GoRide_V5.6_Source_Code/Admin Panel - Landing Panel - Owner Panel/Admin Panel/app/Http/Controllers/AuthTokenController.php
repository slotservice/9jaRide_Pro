<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Contract\Auth;

class AuthTokenController extends Controller
{
    // Fallback Termii key so verification still works before TERMII_API_KEY is
    // added to the VPS .env. Prefer config('services.termii.key').
    private const TERMII_KEY_FALLBACK = 'TLQWUgFmliowHdqTGgUDjIRhIkIgVDHIEexBOIfHpIZkZOKQBrXEGuGGtFeFMs';

    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Mint a Firebase sign-in token, but ONLY after proving the caller actually
     * received the OTP for the phone number they are claiming.
     *
     * Without this the endpoint is a full authentication bypass: it would issue a
     * valid login token for any phone number on request. That matters even more
     * now that driver payouts are automatic — an attacker could sign in as a
     * driver, point the bank details at themselves and have the payout job send
     * the money within a minute.
     *
     * Both apps already send phone + pin_id + pin, so this needs no app rebuild.
     */
    public function customToken(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        $pinId = trim($request->input('pin_id', ''));
        $pin   = trim($request->input('pin', ''));

        if (empty($phone)) {
            return response()->json(['error' => 'Phone number is required'], 400);
        }
        if (empty($pinId) || empty($pin)) {
            return response()->json(['error' => 'OTP verification is required'], 400);
        }

        try {
            $termiiKey  = config('services.termii.key') ?: self::TERMII_KEY_FALLBACK;
            $termiiBase = rtrim(config('services.termii.base_url') ?: 'https://v3.api.termii.com', '/');

            $verifyResponse = Http::asJson()->timeout(20)->post($termiiBase . '/api/sms/otp/verify', [
                'api_key' => $termiiKey,
                'pin_id'  => $pinId,
                'pin'     => $pin,
            ]);

            // Termii returns verified:true on success. Accept a boolean or the
            // string "true" (the API has returned both), but nothing else — any
            // other value ("Expired", "Insufficient", missing) is a failure.
            $verified = $verifyResponse->json('verified');
            $isVerified = ($verified === true)
                || (is_string($verified) && strtolower($verified) === 'true');

            if (!$isVerified) {
                return response()->json(['error' => 'Invalid or expired OTP'], 401);
            }

            // Bind the OTP to the phone being claimed. Verifying the pin alone is
            // not enough: a caller could request an OTP to their OWN number and
            // then submit that valid pin together with somebody else's number.
            // Termii echoes back the msisdn the pin was actually sent to, so it
            // must match. Compared on the last 10 digits to stay tolerant of
            // country-code / leading-zero formatting differences.
            $msisdn = $verifyResponse->json('msisdn');
            if (is_string($msisdn) && $msisdn !== '') {
                $claimedDigits = preg_replace('/\D/', '', $phone);
                $sentDigits    = preg_replace('/\D/', '', $msisdn);
                $claimedTail   = substr($claimedDigits, -10);
                $sentTail      = substr($sentDigits, -10);

                if (strlen($claimedTail) < 10 || strlen($sentTail) < 10 || $claimedTail !== $sentTail) {
                    return response()->json(['error' => 'Invalid or expired OTP'], 401);
                }
            }
        } catch (\Throwable $e) {
            // Fail closed: never mint a token when verification could not be completed.
            return response()->json(['error' => 'OTP verification failed'], 502);
        }

        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        try {
            try {
                $user = $this->auth->getUserByPhoneNumber($phone);
                $uid = $user->uid;
            } catch (\Throwable $e) {
                $user = $this->auth->createUser(['phoneNumber' => $phone]);
                $uid = $user->uid;
            }

            $customToken = $this->auth->createCustomToken($uid);
            return response()->json(['token' => $customToken->toString()]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Token generation failed: ' . $e->getMessage()], 500);
        }
    }
}
