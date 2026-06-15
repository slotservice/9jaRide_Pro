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
