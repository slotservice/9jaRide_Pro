<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUser;

class DriverController extends Controller
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
        return view("drivers.index")->with('id',$id);
    }
    public function createDriver(){       
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view("drivers.create")->with('id',$id);
    }

    public function edit($driverId)
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
    	return view('drivers.edit')->with('driverId', $driverId)->with('id',$id);
    }

    public function view($driverId)
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view('drivers.view')->with('driverId', $driverId)->with('id',$id);
    }
    public function driverDocuments($id)
    {
        return view('drivers.documentIndex', compact('id'));
    }
    
    public function driverDocumentUpload($driverId, $id)
    {
        return view('drivers.driverDocumentUpload', compact('driverId', 'id'));
    }
    
}


