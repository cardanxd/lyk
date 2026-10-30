<?php

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

function postFields(array $overrides = []): array
{
    return array_merge(['title' => 'Con galería', 'body' => '<p>a</p>', 'status' => 'draft'], $overrides);
}

it('agrega fotos, videos y un enlace al crear el post', function () {
    $this->post('/admin/posts', postFields([
        'gallery_images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        'gallery_videos' => [UploadedFile::fake()->create('v.mp4', 2000, 'video/mp4')],
        'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
    ]))->assertRedirect('/admin/posts')->assertSessionHasNoErrors();

    $media = Post::first()->media;
    expect($media)->toHaveCount(4)
        ->and($media->pluck('type')->all())->toBe(['image', 'image', 'video', 'embed'])
        ->and($media->last()->url)->toBe('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
    $media->whereIn('type', ['image', 'video'])->each(fn ($m) => Storage::disk('public')->assertExists($m->path));
});

it('acepta video .mov de iPhone', function () {
    $this->post('/admin/posts', postFields(['gallery_videos' => [UploadedFile::fake()->create('IMG_0001.MOV', 3000, 'video/quicktime')]]))
        ->assertSessionHasNoErrors();
    expect(PostMedia::first()->type)->toBe('video');
});

it('rechaza un enlace de video que no es de YouTube o Facebook', function () {
    $this->post('/admin/posts', postFields(['video_url' => 'https://evil.com/x']))
        ->assertSessionHasErrors(['video_url' => 'Pega un enlace de un video de YouTube o Facebook.']);
    expect(Post::count())->toBe(0)->and(PostMedia::count())->toBe(0);
});

it('rechaza más de 10 fotos en un envío', fn () =>
    $this->post('/admin/posts', postFields(['gallery_images' => array_map(fn ($i) => UploadedFile::fake()->image("$i.jpg"), range(1, 11))]))
        ->assertSessionHasErrors('gallery_images'));

it('rechaza un video de más de 50 MB', fn () =>
    $this->post('/admin/posts', postFields(['gallery_videos' => [UploadedFile::fake()->create('v.mp4', 60000, 'video/mp4')]]))
        ->assertSessionHasErrors('gallery_videos.0'));

it('rechaza un archivo que no es video', fn () =>
    $this->post('/admin/posts', postFields(['gallery_videos' => [UploadedFile::fake()->create('v.pdf', 100, 'application/pdf')]]))
        ->assertSessionHasErrors('gallery_videos.0'));

it('agrega más elementos al editar y actualiza descripción y orden', function () {
    $post = Post::factory()->create();
    $m = PostMedia::factory()->for($post)->create(['caption' => null, 'sort_order' => 5]);
    $this->put("/admin/posts/{$post->id}", postFields([
        'gallery_images' => [UploadedFile::fake()->image('c.jpg')],
        'media' => [$m->id => ['caption' => 'Fachada', 'sort_order' => 0]],
    ]))->assertSessionHasNoErrors();
    expect($m->fresh())->caption->toBe('Fachada')->sort_order->toBe(0)
        ->and($post->media()->count())->toBe(2);
});

it('ignora descripciones de elementos de otro post', function () {
    $post = Post::factory()->create();
    $other = PostMedia::factory()->create(['caption' => 'Original']);
    $this->put("/admin/posts/{$post->id}", postFields(['media' => [$other->id => ['caption' => 'Hack', 'sort_order' => 0]]]));
    expect($other->fresh()->caption)->toBe('Original');
});

it('quitar un elemento borra su archivo', function () {
    $this->post('/admin/posts', postFields(['gallery_images' => [UploadedFile::fake()->image('a.jpg')]]));
    $post = Post::first();
    $m = $post->media->first();
    $this->delete("/admin/posts/{$post->id}/media/{$m->id}")->assertRedirect();
    Storage::disk('public')->assertMissing($m->path);
    expect(PostMedia::count())->toBe(0);
});

it('no permite quitar un elemento de otro post', function () {
    $post = Post::factory()->create();
    $other = PostMedia::factory()->create();
    $this->delete("/admin/posts/{$post->id}/media/{$other->id}")->assertNotFound();
    expect(PostMedia::count())->toBe(1);
});

it('borrar el post borra los archivos de su galería', function () {
    $this->post('/admin/posts', postFields([
        'gallery_images' => [UploadedFile::fake()->image('a.jpg')],
        'gallery_videos' => [UploadedFile::fake()->create('v.mp4', 100, 'video/mp4')],
    ]));
    $post = Post::first();
    $paths = $post->media->pluck('path');
    $this->delete("/admin/posts/{$post->id}");
    $paths->each(fn ($p) => Storage::disk('public')->assertMissing($p));
    expect(PostMedia::count())->toBe(0);
});

it('el formulario muestra la galería actual', function () {
    $post = Post::factory()->create();
    PostMedia::factory()->for($post)->create(['caption' => 'Mi foto']);
    $this->get("/admin/posts/{$post->id}/editar")->assertOk()->assertSee('Galería')->assertSee('Mi foto');
});
