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

    public function customToken(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        $pinId = trim($request->input('pin_id', ''));
        $pin   = trim($request->input('pin', ''));

        if (empty($phone)) {
            return response()->json(['error' => 'Phone number is required'], 400);
        }

        // Security (C1): the OTP must be verified server-side before we mint a
        // Firebase sign-in token. Previously this endpoint trusted the caller and
        // issued a valid token for any phone number with no proof the OTP passed,
        // which was a full authentication bypass. The client now sends the Termii
        // pin_id + pin and we verify them here; client-side verification is removed.
        if (empty($pinId) || empty($pin)) {
            return response()->json(['error' => 'OTP verification is required'], 400);
        }

        try {
            $termiiKey = config('services.termii.key') ?: self::TERMII_KEY_FALLBACK;
            $termiiBase = rtrim(config('services.termii.base_url') ?: 'https://v3.api.termii.com', '/');

            $verifyResponse = Http::asJson()->timeout(20)->post($termiiBase . '/api/sms/otp/verify', [
                'api_key' => $termiiKey,
                'pin_id'  => $pinId,
                'pin'     => $pin,
            ]);

            if ($verifyResponse->json('verified') !== true) {
                return response()->json(['error' => 'Invalid or expired OTP'], 401);
            }
        } catch (\Throwable $e) {
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
