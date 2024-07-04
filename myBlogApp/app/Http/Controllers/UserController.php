<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
// use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|min:3|max:20|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);
        $incomingFields['password'] = Hash::make($incomingFields['password']);
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('succeess', 'Thank you for creating an account!');
    }

    public function loginAPi(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        if(auth()->attempt($incomingFields))
        {
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'false';
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt([
            'username' => $incomingFields['loginusername'],
            'password' => $incomingFields['loginpassword']
        ]))
        {
            $request->session()->regenerate();
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success', 'You have successfully logged in!');
        } 
        else 
        {
            return redirect('/')->with('failure', 'Invalid login!');
        }
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed', ['posts' =>auth()->user()->feedPosts()->latest()->paginate(4)]);
        } else {
            $postCount = Cache::remember('postCount', 20, function(){
                // sleep(5);
                return Post::count();
            });
            return view('homepage', ['postCount' => Post::count()]);
        }
    }

    public function logout()
    {
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out!');
    }

    private function getSharedData($user)
    {
        $currentlyFollowing = 0;
        if(auth()->check())
        {
            $currentlyFollowing = Follow::where([
                ['user_id', '=', auth()->user()->id],
                ['followeduser', '=', $user->id]
            ])->count();
        }
        View::share('sharedData', [
            'username' => $user->username, 
            'postCount' => $user->posts()->count(),
            'avatar' => $user->avatar,
            'currentlyFollowing' => $currentlyFollowing,
            'followerCount' => $user->followers()->count(),
            'followingCount' => $user->following()->count()
        ]);
    }

    public function profile(User $user)
    {
        $this->getSharedData($user);
        return view('profile-posts', [
            'posts' => $user->posts()->latest()->get()
        ]);
    }

    public function profileRaw(User $user)
    {
        return response()->json([
           'theHTML' => view('profile-posts-only', ['posts' => $user->posts()->latest()->get()])->render(),
           'docTitle' => $user->username . "'s Profile" 
        ]);
    }

    public function profileFollowers(User $user)
    {
        $this->getSharedData($user);
        return view('profile-followers', [
            'followers' => $user->followers()->latest()->get(),
        ]);
    }

    public function profileFollowersRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-followers-only', ['followers' => $user->followers()->latest()->get()])->render(),
            'docTitle' => $user->username . "'s Followers" 
         ]);
    }

    public function profileFollowing(User $user)
    {
        $this->getSharedData($user);
        return view('profile-following', [
            'following' => $user->following()->latest()->get(),
        ]);
    }

    public function profileFollowingRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-following-only', ['following' => $user->following()->latest()->get()])->render(),
            'docTitle' => $user->username . "'s Followings" 
         ]);
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:3072'
        ]);

        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';
        // $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        // Storage::put('public/avatars/' . $fileName, $imgData);

        $request->file('avatar')->storeAs('public/avatars/', $filename); //, $imgData

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != "/fallback-avatar.jpg")
        {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Congrats on the new avatar.');
    }

}