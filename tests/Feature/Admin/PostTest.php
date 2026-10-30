<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

it('crea un post publicado con portada', function () {
    $this->post('/admin/posts', [
        'title' => 'Modalidad 40', 'body' => '<p>Hola</p>', 'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'), 'cover' => UploadedFile::fake()->image('c.jpg'),
    ])->assertRedirect('/admin/posts');
    expect(Post::first())->slug->toBe('modalidad-40')->cover_path->not->toBeNull();
    Storage::disk('public')->assertExists(Post::first()->cover_path);
});

it('genera slugs únicos', function () {
    Post::factory()->create(['title' => 'Modalidad 40']);
    expect(Post::factory()->create(['title' => 'Modalidad 40'])->slug)->toBe('modalidad-40-2');
    expect(Post::factory()->create(['title' => 'Modalidad 40'])->slug)->toBe('modalidad-40-3');
});

it('sanitiza HTML peligroso', function () {
    $this->post('/admin/posts', [
        'title' => 'X', 'status' => 'draft',
        'body' => '<p>ok</p><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">l</a>',
    ]);
    expect(Post::first()->body)->toContain('<p>ok</p>')
        ->not->toContain('script')->not->toContain('onerror')->not->toContain('javascript:');
});

it('published() excluye borradores y fechas futuras', function () {
    Post::factory()->create(['status' => 'draft']);
    Post::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]);
    $ok = Post::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);
    expect(Post::published()->pluck('id')->all())->toBe([$ok->id]);
});

it('si se publica sin fecha usa ahora', function () {
    $this->post('/admin/posts', ['title' => 'Sin fecha', 'body' => '<p>a</p>', 'status' => 'published']);
    expect(Post::first()->published_at)->not->toBeNull();
    expect(Post::published()->count())->toBe(1);
});

it('excerptText usa el extracto o el inicio del cuerpo sin HTML', function () {
    expect(Post::factory()->make(['excerpt' => 'Corto'])->excerptText())->toBe('Corto');
    $text = Post::factory()->make(['excerpt' => null, 'body' => '<p>'.str_repeat('a', 300).'</p>'])->excerptText();
    expect($text)->not->toContain('<p>')->and(mb_strlen($text))->toBeLessThanOrEqual(163);
});

it('editar el título de un publicado no cambia su slug', function () {
    $p = Post::factory()->published()->create(['title' => 'Original']);
    $this->put("/admin/posts/{$p->id}", ['title' => 'Nuevo título', 'body' => '<p>a</p>', 'status' => 'published']);
    expect($p->fresh()->slug)->toBe('original');
});

it('borrar un post borra su portada', function () {
    $this->post('/admin/posts', ['title' => 'X', 'body' => '<p>a</p>', 'status' => 'draft', 'cover' => UploadedFile::fake()->image('c.jpg')]);
    $p = Post::first();
    $this->delete("/admin/posts/{$p->id}")->assertRedirect('/admin/posts');
    Storage::disk('public')->assertMissing($p->cover_path);
});

it('lista, filtra y busca posts', function () {
    Post::factory()->create(['title' => 'Borrador AFORE', 'status' => 'draft']);
    Post::factory()->published()->create(['title' => 'Publicado M40']);
    $this->get('/admin/posts')->assertOk()->assertSee(['Borrador AFORE', 'Publicado M40']);
    $this->get('/admin/posts?status=draft')->assertSee('Borrador AFORE')->assertDontSee('Publicado M40');
    $this->get('/admin/posts?q=M40')->assertSee('Publicado M40')->assertDontSee('Borrador AFORE');
    $this->get('/admin/posts/nuevo')->assertOk();
});

it('guarda los títulos del editor (h1 de Trix) como h2', function () {
    $this->post('/admin/posts', ['title' => 'X', 'status' => 'draft', 'body' => '<h1>Sección</h1><div>Texto</div>']);
    expect(Post::first()->body)->toContain('<h2>Sección</h2>')->not->toContain('<h1>');
});

it('el editor recibe los h2 como h1 para no perderlos', function () {
    $p = Post::factory()->create(['body' => '<h2>Sección</h2><p>Texto</p>']);
    $this->get("/admin/posts/{$p->id}/editar")->assertSee('&lt;h1&gt;Sección&lt;/h1&gt;', false);
});
