<?php

namespace App\Http\Controllers;

use App\Models\Post;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect(['home', 'posts.index', 'about', 'contact', 'privacy'])
            ->map(fn ($name) => ['loc' => route($name), 'lastmod' => null])
            ->concat(Post::published()->get()->map(fn (Post $post) => [
                'loc' => route('posts.show', $post),
                'lastmod' => $post->updated_at->toAtomString(),
            ]));

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
