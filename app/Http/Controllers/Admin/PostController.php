<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Models\PostMedia;
use App\Support\ImageUploader;
use App\Support\VideoEmbed;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->when(in_array($request->query('status'), ['draft', 'published'], true), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->query('q').'%'))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post(['status' => 'draft'])]);
    }

    public function store(PostRequest $request)
    {
        $post = new Post($request->postData());

        if ($request->hasFile('cover')) {
            $post->cover_path = ImageUploader::store($request->file('cover'), 'posts');
        }

        $post->save();
        $this->saveMedia($request, $post);

        return redirect()->route('admin.posts.index')->with('status', 'Post creado.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', ['post' => $post]);
    }

    public function update(PostRequest $request, Post $post)
    {
        $post->fill($request->postData());

        if ($request->hasFile('cover')) {
            $post->cover_path = ImageUploader::replace($post->cover_path, $request->file('cover'), 'posts');
        }

        $post->save();
        $this->saveMedia($request, $post);

        return redirect()->route('admin.posts.index')->with('status', 'Post actualizado.');
    }

    /** Agrega fotos, videos y enlace a la galería, y actualiza descripción y orden de lo existente. */
    private function saveMedia(PostRequest $request, Post $post): void
    {
        foreach ($request->validated('media') ?? [] as $id => $fields) {
            $post->media()->whereKey($id)->update([
                'caption' => $fields['caption'] ?? null,
                'sort_order' => (int) ($fields['sort_order'] ?? 0),
            ]);
        }

        $order = (int) $post->media()->max('sort_order') + 1;

        foreach ($request->file('gallery_images', []) as $file) {
            $post->media()->create(['type' => 'image', 'path' => ImageUploader::store($file, 'posts/gallery'), 'sort_order' => $order++]);
        }

        foreach ($request->file('gallery_videos', []) as $file) {
            $post->media()->create(['type' => 'video', 'path' => ImageUploader::store($file, 'posts/videos'), 'sort_order' => $order++]);
        }

        if ($request->filled('video_url')) {
            $post->media()->create(['type' => 'embed', 'url' => VideoEmbed::fromUrl($request->input('video_url')), 'sort_order' => $order]);
        }
    }

    public function destroy(Post $post)
    {
        ImageUploader::delete($post->cover_path);
        $post->media->each(fn (PostMedia $media) => ImageUploader::delete($media->path));
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post eliminado.');
    }
}
