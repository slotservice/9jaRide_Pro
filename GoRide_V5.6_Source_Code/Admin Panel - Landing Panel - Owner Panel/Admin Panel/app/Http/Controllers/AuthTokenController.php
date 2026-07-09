<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;

class AuthTokenController extends Controller
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function customToken(Request $request)
    {
        // NOTE (2026-07-10): server-side OTP verification (C1) was reverted at the
        // client's request to restore working login. This endpoint again mints a
        // Firebase sign-in token from the phone number alone; pin_id/pin are
        // accepted for compatibility with the current app build but not required.
        // This re-opens the pre-C1 auth-bypass and must be re-secured later
        // (planned: move the endpoint behind HTTPS + re-enable OTP verification).
        $phone = trim($request->input('phone', ''));

        if (empty($phone)) {
            return response()->json(['error' => 'Phone number is required'], 400);
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
