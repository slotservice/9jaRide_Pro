<?php
/**
 * File name: AjaxController.php
 * Last modified: 2022.06.11 at 16:10:52
 * Author:Siddhi
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\Auth;

use App\Models\VendorUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Prettus\Validator\Exceptions\ValidatorException;

class AjaxController extends Controller
{

   
    public function setToken(Request $request)
    {
        // Security: verify the caller actually completed a Firebase sign-in for the
        // account being logged into. Without this, anyone could POST a victim's
        // phone/email/id and be logged in as them (account takeover). The client
        // must send the Firebase ID token obtained from the completed OTP / Google
        // sign-in; we verify it server-side and require its uid to match the
        // owner account id being authenticated.
        $idToken = $request->input('idToken');
        if (empty($idToken)) {
            return response()->json(['access' => false, 'error' => 'Authentication token required'], 401);
        }
        try {
            $firebaseAuth = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(storage_path('app/firebase/credentials.json'))
                ->createAuth();
            $verifiedToken = $firebaseAuth->verifyIdToken($idToken);
        } catch (\Throwable $e) {
            return response()->json(['access' => false, 'error' => 'Invalid authentication token'], 401);
        }
        $verifiedUid = $verifiedToken->claims()->get('sub');
        if (empty($verifiedUid) || $verifiedUid !== $request->id) {
            return response()->json(['access' => false, 'error' => 'Authentication mismatch'], 401);
        }

        $uuid        = $request->id;
        $phone       = $request->phone;
        $email       = $request->email;
        $password    = $request->password ?? "12345678";
        $isSubscribed = $request->isSubscribed; 
        if($request->provider == 'google'){
            $exist = User::where('email', $email)->first();
           
            if($exist){
                Auth::login($exist, true);
                $data = array();
                if (Auth::check()) {

                    $data['access'] = true;
                }
            }else{
                $user = User::create([
                    'name' => $request->fullName,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                    'isSubscribed'=> $request->isSubscribed
                ]);

                DB::table('vendor_users')->insert([
                    'user_id' => $user->id,
                    'uuid' => $uuid,
                    'phone' => $phone,
                ]);
                Auth::login($user, true);
                $data = array();
                if (Auth::check()) {
                    $data['access'] = true;
                }
            }
        }else{
     
            $exist = VendorUser::where('phone', $request->phone)->get();
            $data = $exist->isEmpty();
        
            if ($exist->isEmpty()) {
                
                $user = User::create([
                    'name' => $request->fullName,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                    'isSubscribed'=> $request->isSubscribed
                ]);

                DB::table('vendor_users')->insert([
                    'user_id' => $user->id,
                    'uuid' => $uuid,
                    'phone' => $phone,
                ]);

            } else {
                $user = DB::table('vendor_users')->select('id')->where('phone', $phone)->first();
                DB::table('vendor_users')->where('id', $user->id)
                    ->update([
                        'uuid' => $uuid,
                        'phone' => $phone
                    ]);
            }
            User::where('email', $request->email)->update([
                'isSubscribed' => ($request->isSubscribed==null) ? '' : $request->isSubscribed
            ]);
            $user = User::where('email', $request->email)->first();

            Auth::login($user, true);
            $data = array();
            if (Auth::check()) {

                $data['access'] = true;
            }

        }

        

        return $data;

        
    }
    public function setSubcriptionFlag(Request $request)
    {
        User::where('email', $request->email)->update([
            'isSubscribed' => $request->isSubscribed
        ]);

        $data = array();
        if (Auth::check()) {
            $data['access'] = true;
        }


        return $data;
    }


    public function logout(Request $request)
    {

        $user_id = Auth::user()->user_id;
        $user = VendorUser::where('user_id', $user_id)->first();

        try {
            Auth::logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }

        $data1 = array();
        if (! Auth::check()) {
            $data1['logoutuser'] = true;
        }
        return $data1;
    }


}
