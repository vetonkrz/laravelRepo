<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user)
    {
        if($user->id == auth()->user()->id)
        {
            return back()->with('failure', 'You cannot follow yourself!');
        }

        $existCheck = Follow::where([
            ['user_id', '=', auth()->user()->id],
            ['followeduser', '=', $user->id]
        ])->count();
        if($existCheck)
        {
            return back()->with('failure', 'User already being followed!');
        }


        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id; // user thats making the follow
        $newFollow->followeduser = $user->id; // user to follow
        $newFollow->save();
        return back()->with('success', 'User successfully followed!');
    }

    public function removeFollow(User $user)
    {
        Follow::where([
            ['user_id', '=', auth()->user()->id],
            ['followeduser', '=', $user->id]
        ])->delete();

        return back()->with('success', 'User successfully unfollowed!');
    }
}