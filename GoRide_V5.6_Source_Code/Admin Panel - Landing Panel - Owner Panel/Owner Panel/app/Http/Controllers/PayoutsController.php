<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUser;

class PayoutsController extends Controller
{  

   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
    	$user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();
        $id=$exist->uuid;
       	return view("payouts.index")->with('id',$id);
    }

     public function create()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();
        $id=$exist->uuid;
        return view("payouts.create")->with('id',$id);
    }

}