<?php

namespace App\Http\Controllers;
use App\Reactions\Wow;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 📌 List + Search + Pagination
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('body', 'LIKE', "%{$request->search}%");
        }

        $posts = $query->latest()->paginate(3);

        return view('posts.index', compact('posts'));
    }

    // 📌 Toggle Like
    public function like(Post $post)
    {
        $status = $post->toggleMark('like', auth()->user());
        return back()->with('success', $status ? 'Liked!' : 'Unliked!');
    }

    // 📌 Toggle Favorite
    public function favorite(Post $post)
    {
        $status = $post->toggleMark('favorite', auth()->user());
        return back()->with('success', $status ? 'Added to favorites' : 'Removed from favorites');
    }

    // 📌 Toggle Bookmark
    public function bookmark(Post $post)
    {
        $status = $post->toggleMark('bookmark', auth()->user());
        return back()->with('success', $status ? 'Bookmarked' : 'Removed bookmark');
    }

    // 📌 Reaction
    public function react(Request $request, Post $post)
    {
        $post->toggleMark(Wow::class, auth()->user());

        return back()->with('success', 'Reaction updated!');
    }

}