<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::with('marks');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                  ->orWhere('body', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->sort === 'most_liked') {
            $query->withCount('marks')->orderBy('marks_count', 'desc');
        } else {
            $query->latest();
        }

        $posts = $query->paginate(3);

        return view('posts.index', compact('posts'));
    }

    public function dashboard(): View
    {
        $user = Auth::user();

        $stats = [
            'likes'     => $user->marks()->where('type', 'like')->count(),
            'bookmarks' => $user->marks()->where('type', 'bookmark')->count(),
            'favorites' => $user->marks()->where('type', 'favorite')->count(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function like(Post $post): JsonResponse
    {
        $status = $post->toggleMark('like', auth()->user());
        return response()->json(['status' => $status]);
    }

    public function favorite(Post $post): JsonResponse
    {
        $status = $post->toggleMark('favorite', auth()->user());
        return response()->json(['status' => $status]);
    }

    public function bookmark(Post $post): JsonResponse
    {
        $status = $post->toggleMark('bookmark', auth()->user());
        return response()->json(['status' => $status]);
    }
}