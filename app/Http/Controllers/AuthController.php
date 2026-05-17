<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function index(){
        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * This function will validate the email and password provided by the user,
     * and log them in if the credentials are correct. If the credentials are
     * incorrect, it will return a redirect back to the login page with an error
     * message.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        if(Auth::user()->email == 'skelectricals@gmail.com'){
            return redirect()->route('devices.view', ['id' => 2]);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function logout(){
        auth()->logout();
        return redirect('/');
    }
}
