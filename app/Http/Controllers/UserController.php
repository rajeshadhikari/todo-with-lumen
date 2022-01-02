<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required'
         ]);
       $user = User::where('email', $request->input('email'))->first();
      if($user && Hash::check($request->input('password'), $user->password)){
           $apiToken = base64_encode(Str::random(32));
           User::where('email', $request->input('email'))->update(['api_token' => "$apiToken"]);;
           return response()->json(['status' => 'success','api_token' => $apiToken]);
       }else{
           return response()->json(['status' => 'fail'], 401);
       }
    }
}
