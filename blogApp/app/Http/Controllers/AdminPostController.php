<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $incomingFields = $request->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:3'
        ]);
        $post->update($incomingFields);
        return to_route('admin');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return to_route('admin');
    }
}
