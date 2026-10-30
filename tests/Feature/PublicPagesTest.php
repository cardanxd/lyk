<?php

use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\Settings;

it('home muestra portadas, servicios, últimos 3 posts, testimonios y FAQ activos', function () {
    HeroSlide::factory()->create(['title' => 'Portada A']);
    HeroSlide::factory()->create(['title' => 'Portada oculta', 'is_active' => false]);
    Service::factory()->create(['title' => 'Modalidad 40']);
    Post::factory()->published()->count(4)->sequence(fn ($s) => ['title' => "Post {$s->index}"])->create();
    Faq::factory()->create(['question' => '¿Qué es la AFORE?']);
    Testimonial::factory()->create(['name' => 'Doña Rosa']);

    $this->get('/')->assertOk()
        ->assertSee(['Portada A', 'Modalidad 40', '¿Qué es la AFORE?', 'Doña Rosa'])
        ->assertDontSee('Portada oculta')
        ->assertViewHas('posts', fn ($p) => $p->count() === 3);
});

it('home sin contenido usa la portada de marca y oculta secciones vacías', fn () =>
    $this->get('/')->assertOk()->assertSee('images/brand/portada.jpg')
        ->assertDontSee('Preguntas frecuentes')->assertDontSee('Lo que dicen'));

it('lista posts paginados de 9 en 9', function () {
    Post::factory()->published()->count(10)->create();
    $this->get('/posts')->assertOk()->assertViewHas('posts', fn ($p) => $p->count() === 9 && $p->total() === 10);
});

it('muestra un post publicado con botones para compartir', function () {
    $p = Post::factory()->published()->create(['title' => 'Guía Modalidad 40', 'body' => '<p>Contenido de la guía</p>']);
    $this->get("/posts/{$p->slug}")->assertOk()
        ->assertSee(['Guía Modalidad 40', 'Contenido de la guía'])
        ->assertSee('https://wa.me/?text=', false)
        ->assertSee('https://www.facebook.com/sharer/sharer.php?u=', false);
});

it('no muestra borradores', function () {
    $d = Post::factory()->create(['status' => 'draft']);
    $this->get("/posts/{$d->slug}")->assertNotFound();
});

it('no muestra posts programados a futuro', function () {
    $f = Post::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]);
    $this->get("/posts/{$f->slug}")->assertNotFound();
});

it('nosotros muestra misión, visión y valores editables', function () {
    app(Settings::class)->setMany(['mission' => 'Misión X', 'vision' => 'Visión Y', 'values' => "Honestidad\nCercanía"]);
    $this->get('/nosotros')->assertOk()->assertSee(['Misión X', 'Visión Y', 'Honestidad', 'Cercanía']);
});

it('nosotros escapa el HTML de los ajustes', function () {
    app(Settings::class)->set('mission', '<script>x</script>');
    $this->get('/nosotros')->assertOk()->assertDontSee('<script>x</script>', false);
});

it('aviso de privacidad muestra el texto guardado', function () {
    app(Settings::class)->set('privacy_notice', 'Tus datos están protegidos');
    $this->get('/aviso-de-privacidad')->assertOk()->assertSee('Tus datos están protegidos');
});

it('el pie muestra teléfono, correo y redes configurados', function () {
    app(Settings::class)->setMany(['phone' => '55 1111 2222', 'email' => 'hola@lyk.mx', 'facebook_url' => 'https://facebook.com/lyk']);
    $this->get('/')->assertSee(['55 1111 2222', 'hola@lyk.mx', 'https://facebook.com/lyk']);
});
