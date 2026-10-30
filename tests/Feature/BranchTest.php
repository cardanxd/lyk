<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function validBranch(array $overrides = []): array
{
    return array_merge([
        'name' => 'Sucursal Centro',
        'address' => 'Av. Juárez 123, Col. Centro',
        'city' => 'Puebla, Pue.',
        'phone' => '222 123 4567',
        'schedule' => 'Lunes a viernes 9:00 a 18:00',
        'maps_url' => 'https://maps.app.goo.gl/abc',
        'sort_order' => 0,
        'is_active' => 1,
    ], $overrides);
}

describe('panel', function () {
    beforeEach(fn () => $this->actingAs(User::factory()->create()));

    it('crea una sucursal con foto', function () {
        $this->post('/admin/sucursales', validBranch(['photo' => UploadedFile::fake()->image('s.jpg')]))
            ->assertRedirect('/admin/sucursales');
        $b = Branch::first();
        expect($b->name)->toBe('Sucursal Centro');
        Storage::disk('public')->assertExists($b->photo_path);
    });

    it('editar sin foto nueva conserva la actual', function () {
        $this->post('/admin/sucursales', validBranch(['photo' => UploadedFile::fake()->image('s.jpg')]));
        $b = Branch::first();
        $this->put("/admin/sucursales/{$b->id}", validBranch(['name' => 'Matriz']))->assertSessionHasNoErrors();
        expect($b->fresh())->name->toBe('Matriz')->photo_path->toBe($b->photo_path);
    });

    it('borrar una sucursal borra su foto', function () {
        $this->post('/admin/sucursales', validBranch(['photo' => UploadedFile::fake()->image('s.jpg')]));
        $b = Branch::first();
        $this->delete("/admin/sucursales/{$b->id}")->assertRedirect('/admin/sucursales');
        Storage::disk('public')->assertMissing($b->photo_path);
    });

    it('valida campos requeridos, URLs y WhatsApp', fn () =>
        $this->post('/admin/sucursales', validBranch(['name' => '', 'address' => '', 'maps_url' => 'abc', 'map_embed_url' => 'abc', 'whatsapp' => 'xx']))
            ->assertSessionHasErrors(['name', 'address', 'maps_url', 'map_embed_url', 'whatsapp']));

    it('muestra listado, alta y edición', function () {
        $b = Branch::factory()->create(['name' => 'Sucursal Norte']);
        $this->get('/admin/sucursales')->assertOk()->assertSee('Sucursal Norte');
        $this->get('/admin/sucursales/nuevo')->assertOk();
        $this->get("/admin/sucursales/{$b->id}/editar")->assertOk();
    });
});

describe('sitio', function () {
    it('la página de sucursales muestra el total y los datos', function () {
        Branch::factory()->create(validBranch(['photo_path' => 'branches/a.jpg']));
        Branch::factory()->create(['name' => 'Sucursal Norte']);
        Branch::factory()->create(['name' => 'Sucursal Cerrada', 'is_active' => false]);

        $this->get('/sucursales')->assertOk()
            ->assertSee('Contamos con 2 sucursales')
            ->assertSee(['Sucursal Centro', 'Av. Juárez 123, Col. Centro', 'Puebla, Pue.', 'Sucursal Norte', 'branches/a.jpg'])
            ->assertSee('href="https://maps.app.goo.gl/abc"', false)
            ->assertDontSee('Sucursal Cerrada');
    });

    it('usa singular con una sola sucursal', function () {
        Branch::factory()->create();
        $this->get('/sucursales')->assertSee('Contamos con 1 sucursal')->assertDontSee('1 sucursales');
    });

    it('sin sucursales no las anuncia en inicio ni en el menú', function () {
        $this->get('/sucursales')->assertOk()->assertSee('Muy pronto');
        $this->get('/')->assertDontSee('Nuestras sucursales')->assertDontSee('href="'.route('branches').'"', false);
    });

    it('con sucursales aparecen en inicio, menú y contacto', function () {
        Branch::factory()->create(['name' => 'Sucursal Sur']);
        $this->get('/')->assertSee('Nuestras sucursales')->assertSee('Sucursal Sur')
            ->assertSee('href="'.route('branches').'"', false);
        $this->get('/contacto')->assertSee('Sucursal Sur');
    });

    it('el sitemap incluye sucursales', fn () =>
        $this->get('/sitemap.xml')->assertSee(route('branches'), false));
});
