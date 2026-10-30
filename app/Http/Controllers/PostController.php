<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public const PER_PAGE = 9;

    public function index()
    {
        return view('posts.index', ['posts' => Post::published()->withMediaCounts()->paginate(self::PER_PAGE)]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->with('media')->where('slug', $slug)->firstOrFail();

        return view('posts.show', [
            'post' => $post,
            'related' => Post::published()->withMediaCounts()->whereKeyNot($post->id)->limit(3)->get(),
        ]);
    }
}
