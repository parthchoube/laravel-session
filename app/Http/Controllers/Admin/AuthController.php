<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\User;

class AuthController extends Controller
{

	function login(){
		if(!Auth::check()) {
	        return view('admin.login');;
	    } else {
	    	return redirect()->intended('admin/dashboard');
	    }
	}

	function doLogin(Request $request){

        $credentials = $request->only('email', 'password');
        $remember = $request->only('remember');
        $rule  =  [
            'email' => 'required',
            'password' => 'required',
        ];
    	$validator = Validator::make($credentials,$rule);
    	
        if ($validator->fails()) {
    		return redirect('admin/login')->withErrors($validator)->withInput();
    	}

		if (Auth::attempt($credentials, $remember)) {
			$user = auth()->user();
			if($user->isAdmin){
            	return redirect()->intended('admin/dashboard');
			}
        }

        return redirect('admin/login')->withErrors(['invalid email and password.'])->withInput();
	}

	public function logout(Request $request) {
		Auth::logout();
	  	return redirect()->route('admin.login');
	}
}