<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/timezone', function () {
    return response()->json([
        'timezone' => config('app.timezone'),
    ]);
});

Route::post('/auth/custom-token', [App\Http\Controllers\AuthTokenController::class, 'customToken'])->name('auth.custom-token');

// Texts the rider their own ride start code. Throttled because every send
// costs money, on top of the signed in ownership check inside the controller.
Route::post('/ride/send-otp-sms', [App\Http\Controllers\RideOtpController::class, 'sendOtpSms'])
    ->middleware('throttle:5,10')
    ->name('ride.send-otp-sms');
