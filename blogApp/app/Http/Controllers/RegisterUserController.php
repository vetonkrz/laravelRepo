<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterUserController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'name' => 'required|min:3|max:15',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create($incomingFields);
        auth()->login($user);
        return to_route('posts.index');
    }
}
