<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\VendorUser;

class TransactionController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth');
    }

    public function ownerWalletTranscation()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view("transaction.owner_transaction")->with('id',$id);
    }

}
