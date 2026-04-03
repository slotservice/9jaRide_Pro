<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUser;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
    	return view('home')->with('id', $id);
        
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()    
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUser::where('user_id',$id)->first();        
        $id=$exist->uuid;
        return view('dashboard')->with('id', $id);
    }    
    
    public function users()
    {
        return view('users');
    }

    public function storeFirebaseService(Request $request){
		if(!empty($request->serviceJson) && !Storage::disk('local')->has('firebase/credentials.json')){
			Storage::disk('local')->put('firebase/credentials.json',file_get_contents(base64_decode($request->serviceJson)));
		}
	}
    
}
