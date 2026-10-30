<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostMedia;
use App\Support\ImageUploader;

class PostMediaController extends Controller
{
    public function destroy(Post $post, PostMedia $media)
    {
        ImageUploader::delete($media->path);
        $media->delete();

        return redirect()->route('admin.posts.edit', $post->id)->with('status', 'Elemento quitado de la galería.');
    }
}
