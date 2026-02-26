<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\User;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index', ['posts' => Post::all()]);
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function bookmark(Post $post)
    {
        $post->mark('bookmark', auth()->user());
        return redirect()->back();
    }

    public function like(Post $post)
    {
        $post->mark('like', auth()->user());
        return redirect()->back();
    }

    public function favorite(Post $post)
    {
        $post->mark('favorite', auth()->user());
        return redirect()->back();
    }

    public function react(Request $request, Post $post)
    {
        $post->mark($request->type, auth()->user());
        return redirect()->back();
    }
}