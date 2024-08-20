<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function home()
    {
        $myName = "Tee";
        $animals = ['Dogs', 'Cats', 'Squirrels'];
        return view('homepage', ['name' => $myName, 'animals'=>$animals]);
    }

    public function post()
    {
        return view('single-post');
    }
}
