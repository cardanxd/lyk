<?php

use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

it('lista las portadas', function () {
    HeroSlide::factory()->create(['title' => 'Portada uno']);
    $this->get('/admin/portadas')->assertOk()->assertSee('Portada uno');
});

it('crea una portada con imagen', function () {
    $this->post('/admin/portadas', [
        'image' => UploadedFile::fake()->image('p.jpg', 1600, 900),
        'title' => 'Hola', 'sort_order' => 1, 'is_active' => 1,
    ])->assertRedirect('/admin/portadas');
    $slide = HeroSlide::first();
    expect($slide->title)->toBe('Hola')->and($slide->is_active)->toBeTrue();
    Storage::disk('public')->assertExists($slide->image_path);
});

it('exige imagen al crear portada pero no al editar', function () {
    $this->post('/admin/portadas', ['title' => 'x'])->assertSessionHasErrors('image');
    $s = HeroSlide::factory()->create();
    $this->put("/admin/portadas/{$s->id}", ['title' => 'y', 'sort_order' => 0])
        ->assertRedirect('/admin/portadas')->assertSessionHasNoErrors();
    expect($s->fresh())->title->toBe('y')->is_active->toBeFalse();
});

it('reemplazar la imagen de una portada borra la anterior', function () {
    $this->post('/admin/portadas', ['image' => UploadedFile::fake()->image('a.jpg'), 'sort_order' => 0]);
    $s = HeroSlide::first();
    $old = $s->image_path;
    $this->put("/admin/portadas/{$s->id}", ['image' => UploadedFile::fake()->image('b.jpg'), 'sort_order' => 0]);
    Storage::disk('public')->assertMissing($old)->assertExists($s->fresh()->image_path);
});

it('borrar una portada borra su archivo', function () {
    $this->post('/admin/portadas', ['image' => UploadedFile::fake()->image('p.jpg'), 'sort_order' => 0]);
    $s = HeroSlide::first();
    $this->delete("/admin/portadas/{$s->id}")->assertRedirect('/admin/portadas');
    Storage::disk('public')->assertMissing($s->image_path);
    expect(HeroSlide::count())->toBe(0);
});

it('rechaza un botón con URL inválida', fn () =>
    $this->post('/admin/portadas', ['image' => UploadedFile::fake()->image('p.jpg'), 'button_url' => 'javascript:alert(1)'])
        ->assertSessionHasErrors('button_url'));

it('acepta un botón con ruta interna', fn () =>
    $this->post('/admin/portadas', ['image' => UploadedFile::fake()->image('p.jpg'), 'button_text' => 'Contacto', 'button_url' => '/contacto'])
        ->assertRedirect('/admin/portadas')->assertSessionHasNoErrors());

it('crea un servicio', function () {
    $this->post('/admin/servicios', ['title' => 'Modalidad 40', 'description' => 'D', 'icon' => 'calendar', 'is_active' => 1])
        ->assertRedirect('/admin/servicios');
    expect(Service::first()->title)->toBe('Modalidad 40');
});

it('rechaza un icono de servicio fuera de la lista', fn () =>
    $this->post('/admin/servicios', ['title' => 'T', 'description' => 'D', 'icon' => 'rocket'])
        ->assertSessionHasErrors('icon'));

it('CRUD de preguntas', function () {
    $this->post('/admin/preguntas', ['question' => '¿Qué es?', 'answer' => 'Algo', 'is_active' => 1])->assertRedirect('/admin/preguntas');
    $f = Faq::first();
    $this->put("/admin/preguntas/{$f->id}", ['question' => '¿Qué es la AFORE?', 'answer' => 'Algo', 'is_active' => 1]);
    $this->assertDatabaseHas('faqs', ['question' => '¿Qué es la AFORE?']);
    $this->delete("/admin/preguntas/{$f->id}");
    $this->assertDatabaseCount('faqs', 0);
});

it('CRUD de testimonios con foto', function () {
    $this->post('/admin/testimonios', ['name' => 'Doña Rosa', 'quote' => 'Excelente', 'photo' => UploadedFile::fake()->image('r.jpg'), 'is_active' => 1])
        ->assertRedirect('/admin/testimonios');
    $t = Testimonial::first();
    Storage::disk('public')->assertExists($t->photo_path);
    $this->put("/admin/testimonios/{$t->id}", ['name' => 'Rosa M.', 'quote' => 'Excelente', 'is_active' => 1]);
    expect($t->fresh()->name)->toBe('Rosa M.')->and($t->fresh()->photo_path)->toBe($t->photo_path);
    $this->delete("/admin/testimonios/{$t->id}");
    Storage::disk('public')->assertMissing($t->photo_path);
});

it('muestra listado, alta y edición de cada sección', function (string $path, string $model) {
    $item = $model::factory()->create();
    $this->get("/admin/$path")->assertOk();
    $this->get("/admin/$path/nuevo")->assertOk();
    $this->get("/admin/$path/{$item->id}/editar")->assertOk();
})->with([
    ['portadas', HeroSlide::class],
    ['servicios', Service::class],
    ['preguntas', Faq::class],
    ['testimonios', Testimonial::class],
]);
