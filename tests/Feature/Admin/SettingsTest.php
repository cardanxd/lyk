<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

it('muestra la pantalla de ajustes', fn () =>
    $this->get('/admin/ajustes')->assertOk()->assertSee('Misión'));

it('actualiza misión, visión y contacto', function () {
    $this->put('/admin/ajustes', validSettings(['mission' => 'M', 'vision' => 'V', 'phone' => '55 1234 5678']))
        ->assertRedirect('/admin/ajustes')->assertSessionHas('status');
    expect(setting('mission'))->toBe('M')->and(setting('vision'))->toBe('V')
        ->and(setting('phone'))->toBe('55 1234 5678');
});

it('cambia el logo y borra el anterior', function () {
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('a.png')]));
    $old = setting('logo_path');
    expect($old)->not->toBeNull();
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('b.png')]));
    Storage::disk('public')->assertMissing($old)->assertExists(setting('logo_path'));
});

it('conserva el logo si no se sube uno nuevo', function () {
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('a.png')]));
    $path = setting('logo_path');
    expect($path)->not->toBeNull();
    $this->put('/admin/ajustes', validSettings());
    expect(setting('logo_path'))->toBe($path);
});

it('rechaza imágenes de más de 5 MB o HEIC con error en español', function () {
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->create('f.heic', 12000)]))
        ->assertSessionHasErrors('logo');
    expect(setting('logo_path'))->toBeNull();
    expect(session('errors')->first('logo'))->toStartWith('El campo logo');
});

it('rechaza un JPG de más de 5 MB', fn () =>
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('big.jpg')->size(6000)]))
        ->assertSessionHasErrors('logo'));

it('valida email y URLs de redes', fn () =>
    $this->put('/admin/ajustes', validSettings(['email' => 'no-es-email', 'facebook_url' => 'abc']))
        ->assertSessionHasErrors(['email', 'facebook_url']));

it('valida el formato de WhatsApp', fn () =>
    $this->put('/admin/ajustes', validSettings(['whatsapp' => 'abc']))->assertSessionHasErrors('whatsapp'));
