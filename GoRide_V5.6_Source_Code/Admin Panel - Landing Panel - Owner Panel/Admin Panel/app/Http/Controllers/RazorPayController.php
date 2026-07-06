<?php
/**
 * File name: RazorPayController.php
 * Last modified: 2022.03.08 at 16:03:23
 * Author: Siddhi infosoft
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorPayController extends Controller
{
    // Reject calls for a payment gateway that is not enabled server-side. These
    // routes are unauthenticated and accept client-supplied secret keys (C2); the
    // guard ensures a disabled gateway (all except Paystack/cash/wallet today)
    // cannot be driven through this endpoint. Fails closed on any error.
    private function gatewayEnabled(string $gateway): bool
    {
        try {
            $db = app(\Kreait\Firebase\Contract\Firestore::class)->database();
            $snap = $db->collection('settings')->document('payment')->snapshot();
            if (!$snap->exists()) {
                return false;
            }
            $data = $snap->data();
            $g = $data[$gateway] ?? null;
            return is_array($g) && (($g['enable'] ?? false) === true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function createOrderid(Request $request)
    {
        if (!$this->gatewayEnabled('razorpay')) {
            return response()->json(['error' => 'Payment method not available'], 403);
        }

        $input = $request->all();
        $amount = $input['amount'];
        $receipt_id = $input['receipt_id'];
        $currency = $input['currency'];
        $razorpaykey = $input['razorpaykey'];
        $razorPaySecret = $input['razorPaySecret'];
        
        try {
            
          $client = new Api($razorpaykey, $razorPaySecret);
            
            $order  = $client->order->create([
                'receipt'  => $receipt_id,
                'amount'   => $amount,
                'currency' => $currency
            ]);

            $attributes = $this->getProtectedValue($order,'attributes');

            return response()->json($attributes);

        }catch(Exception $e) {
          
            return response()->json(array('faild' => $e->getMessage()));
        }

    }

    public function getProtectedValue($obj, $name) {
      $array = (array)$obj;
      $prefix = chr(0).'*'.chr(0);
      return $array[$prefix.$name];
    }
}
