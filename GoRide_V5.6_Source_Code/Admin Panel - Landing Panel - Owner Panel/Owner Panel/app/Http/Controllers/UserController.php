<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VendorUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth'); 
    }

	  

  public function profile()
  {
      $user = Auth::user();
      $id = Auth::id();
      $vendorUser = VendorUser::where('user_id',$id)->first();        
      $id=$vendorUser->uuid;   
      return view('users.profile', compact(['user','id','vendorUser']));
  }

  public function update(Request $request,$id){
    
    $name = $request->input('name');
   
    $email = $request->input('email');    
  
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
      
    ]);   
    

    if ($validator->fails()) {
      $error = $validator->errors()->first();
      return Redirect()->back()->with(['message' => $error]);
    } 
    
    $user = User::find($id);
    if($user) {
      $user->name = $name;      
      $user->save();
    }

    return redirect()->back();
  }

  
  

}
