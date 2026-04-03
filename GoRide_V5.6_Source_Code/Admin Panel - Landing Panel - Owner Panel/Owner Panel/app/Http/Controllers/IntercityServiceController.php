<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUser;
class IntercityServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function ridesList(){
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view('intercity_service.ride-list')->with('id',$id);
        
    }

    public function rideView($id){
        return view('intercity_service.ride-view', compact('id'));
    }

}
