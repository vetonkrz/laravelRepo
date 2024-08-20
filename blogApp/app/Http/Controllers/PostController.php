<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewPostMailJob;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Cache::remember('posts', 10, function(){
            return Post::paginate(4);
        });
        return view('posts.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:3',
            'thumbnail' => 'required|image'
        ]);
        // $incomingFields['user_id'] = auth()->id();
        // Post::create($incomingFields);
        $incomingFields['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        auth()->user()->posts()->create($incomingFields);

        dispatch(new SendNewPostMailJob([
            'email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'title' => $incomingFields['title']
        ]));
        return to_route('posts.index')->with('message', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        return view('posts.edit', ['post'=>$post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);
        $incomingFields = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:3',
            'thumbnail' => 'sometimes|image'
        ]);
        // Check if a new thumbnail is uploaded
        if ($request->hasFile('thumbnail')) {
            // Delete the old thumbnail if it exists
            if ($post->thumbnail) {
                $oldThumbnailPath = storage_path('app/public/' . $post->thumbnail);
                if (File::exists($oldThumbnailPath)) {
                    File::delete($oldThumbnailPath);
                }
            }
            // Store the new thumbnail
            $incomingFields['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }
        // Update the post with the new or existing thumbnail
        $post->update($incomingFields);
    
        return to_route('posts.index')->with('message', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        File::delete(storage_path('app/public/' . $post->thumbnail));
        $post->delete();
        return to_route('posts.index');
    }
}
