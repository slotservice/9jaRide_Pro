<?php

namespace App\Providers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\AuthException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FirebaseAuthService
{
    public function storeFirebaseService(Request $request){

        if (!auth()->check()) {
            abort(403);
        }

        if (empty($request->serviceJson)) {
            return response()->json(['error' => 'Missing service JSON'], 400);
        }

        // The client base64-encodes the raw service-account JSON string. Decode it
        // and write the content directly — never treat it as a path/URL (SSRF).
        $decoded = base64_decode($request->serviceJson, true);
        if ($decoded === false) {
            return response()->json(['error' => 'Invalid encoding'], 422);
        }

        // Only accept a well-formed Firebase service-account credential file.
        $json = json_decode($decoded, true);
        if (!is_array($json)
            || (($json['type'] ?? '') !== 'service_account')
            || empty($json['project_id'])
            || empty($json['private_key'])
            || empty($json['client_email'])) {
            return response()->json(['error' => 'Invalid service account file'], 422);
        }

        Storage::disk('local')->put('firebase/credentials.json', $decoded);

        return response()->json(['success' => true]);
    }

    public function getFirebaseToken()
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (request()->cookie('firebase_token')) {
            return request()->cookie('firebase_token');
        }

        //custom random user id
        $uid = "2cPMOVc73PFDsuPfWZ4R";

        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/credentials.json'))->createAuth();
        
        $firebase_token = $firebase->createCustomToken($uid)->toString();

        return response()->json(['firebase_token' => $firebase_token]);
    }
}
