<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUserController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $incomingfields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        if(Auth::guard('web')->attempt([
            'email' => $incomingfields['email'],
            'password' => $incomingfields['password']
        ])){
            return redirect()->intended(route('posts.index'));
        }else{
            return back()->withErrors([
                'emails' => 'The provided credentials do not match our records!'
            ]);
        }
    }
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('posts.index');
    }
}
