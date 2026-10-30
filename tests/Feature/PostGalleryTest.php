<?php

use App\Models\Post;
use App\Models\PostMedia;

it('el post muestra fotos, videos y enlaces de su galería', function () {
    $post = Post::factory()->published()->create();
    PostMedia::factory()->for($post)->create(['path' => 'posts/gallery/a.jpg', 'caption' => 'Nuestra oficina']);
    PostMedia::factory()->for($post)->video()->create(['path' => 'posts/videos/v.mp4']);
    PostMedia::factory()->for($post)->embed()->create();

    $this->get("/posts/{$post->slug}")->assertOk()
        ->assertSee('Galería')
        ->assertSee('posts/gallery/a.jpg', false)
        ->assertSee('loading="lazy"', false)
        ->assertSee('Nuestra oficina')
        ->assertSee('<video', false)->assertSee('controls preload="metadata" playsinline', false)
        ->assertSee('posts/videos/v.mp4', false)
        ->assertSee('Descargar el video', false)
        ->assertSee('<iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"', false)
        ->assertSee('data-gallery', false);
});

it('un post sin galería no muestra la sección', function () {
    $post = Post::factory()->published()->create();
    $this->get("/posts/{$post->slug}")->assertOk()->assertDontSee('data-gallery', false);
});

it('la tarjeta del post indica cuántas fotos y videos tiene', function () {
    $post = Post::factory()->published()->create();
    PostMedia::factory()->for($post)->count(3)->create();
    PostMedia::factory()->for($post)->video()->create();
    $this->get('/posts')->assertSee('3 fotos · 1 video');
});

it('usa singular con una foto', function () {
    $post = Post::factory()->published()->create();
    PostMedia::factory()->for($post)->create();
    $this->get('/posts')->assertSee('1 foto')->assertDontSee('1 fotos');
});

it('los límites de PHP permiten videos de 50 MB', function () {
    $ini = file_get_contents(public_path('.user.ini'));
    expect($ini)->toContain('upload_max_filesize = 60M')->toContain('post_max_size = 128M');
});

it('la página 413 menciona el límite de videos', function () {
    $this->actingAs(App\Models\User::factory()->create());
    $this->call('POST', '/admin/posts', [], [], [], ['CONTENT_LENGTH' => 500 * 1024 * 1024, 'CONTENT_TYPE' => 'multipart/form-data'])
        ->assertStatus(413)->assertSee('50 MB');
});
