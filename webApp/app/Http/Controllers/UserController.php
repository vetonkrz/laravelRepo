<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Events\myAppEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|min:3|max:25|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank you for creating an account!');
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
            ])) {
            $request->session()->regenerate();
            event(new myAppEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success', 'You have successfully logged in!');
        } else {
            return redirect('/')->with('failure', 'Invalid login.');
        }
        
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('homepage-feed', ['username' => auth()->user()->username, 
                                          'posts'=>auth()->user()->feedPosts()->latest()->paginate(3)]);
        } else {
            $postCount = Cache::remember('postCount', 20, function(){
                return Post::count();
            });
            return view('homepage', ['postCount' => $postCount]);
        }
        
    }

    public function logout()
    {
        event(new myAppEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');
    }

    private function getSharedData($user)
    {
        $currentlyFollowing = 0;
        if(auth()->check()){
            $currentlyFollowing = Follow::where([
                ['user_id', '=', auth()->user()->id],
                ['followeduser', '=', $user->id]
            ])->count();
        }
        View::share('sharedData', [
            'username' => $user->username, 
            'avatar' => $user->avatar,
            'currentlyFollowing' => $currentlyFollowing,
            'postCount' => $user->posts()->count(),
            'followerCount' =>$user->followers()->count(),
            'followingCount'=>$user->followingTheseUsers()->count()
        ]);
    }

    public function profile(User $user)
    {
        $this->getSharedData($user);
        return view('profile-posts', [
            'posts' => $user->posts()->latest()->get(),
        ]);
    }

    public function profileRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-posts-only', ['posts' => $user->posts()->latest()->get()])->render(),
            'docTitle' =>$user->username . "'s Profile"
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
            'docTitle' =>$user->username . "'s Followers"
        ]);
    }

    public function profileFollowing(User $user)
    {
        $this->getSharedData($user);
        return view('profile-following', [
            'following' => $user->followingTheseUsers()->latest()->get(),
        ]);
    }

    public function profileFollowingRaw(User $user)
    {
        return response()->json([
            'theHTML' => view('profile-following-only', ['following' => $user->followingTheseUsers()->latest()->get()])->render(),
            'docTitle' =>'Who '.$user->username . " Follows"
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

        // Create image manager with desired driver
        $manager = new ImageManager(new Driver());
        // Read image from file system (uploaded file)
        $image = $manager->read($request->file('avatar')->getPathname());
        // Scale image to maintain aspect ratio
        $image->resize(height:120, width:120);
        // Encode image in jpg format
        $imgData = $image->toJpeg();
        // Save encoded image to the storage
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;
        
        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != "/fallback-avatar.jpg"){
            Storage::delete(str_replace("/storage/", "public/", "$oldAvatar"));
        }
        return back()->with('success', 'Congrats on the new avatar.');
    }

    public function loginAPI(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($incomingFields)){
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'false!';
    }
}
