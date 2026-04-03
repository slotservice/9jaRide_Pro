<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUser;

class SubscriptionPlanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }    
    
    public function SubscriptionHistory()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view('subscription_plans.history')->with('id',$id);
    }
   
}
