<?php

use App\Models\Post;
use App\Support\Settings;

it('muestra el botón de WhatsApp sólo si hay número', function () {
    $this->get('/')->assertDontSee('aria-label="Escríbenos por WhatsApp"', false);
    app(Settings::class)->set('whatsapp', '+525512345678');
    $this->get('/')->assertSee('https://wa.me/525512345678?text=', false)
        ->assertSee('aria-label="Escríbenos por WhatsApp"', false);
});

it('la home tiene descripción y metas Open Graph por defecto', function () {
    app(Settings::class)->set('seo_description', 'Asesoría de pensiones');
    $this->get('/')
        ->assertSee('<meta name="description" content="Asesoría de pensiones">', false)
        ->assertSee('<meta property="og:image" content="'.asset('images/brand/portada.jpg').'">', false);
});

it('el post tiene metas Open Graph con su portada', function () {
    $p = Post::factory()->published()->create(['title' => 'Guía M40', 'cover_path' => 'posts/c.jpg']);
    $this->get("/posts/{$p->slug}")
        ->assertSee('<meta property="og:title" content="Guía M40', false)
        ->assertSee('<meta property="og:type" content="article">', false)
        ->assertSee('posts/c.jpg', false);
});

it('sitemap incluye páginas y posts publicados', function () {
    $p = Post::factory()->published()->create();
    $d = Post::factory()->create(['status' => 'draft']);
    $this->get('/sitemap.xml')->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('posts.show', $p), false)
        ->assertSee(route('about'), false)
        ->assertDontSee($d->slug, false);
});

it('robots.txt bloquea el panel', fn () =>
    expect(file_get_contents(public_path('robots.txt')))->toContain('Disallow: /admin'));
