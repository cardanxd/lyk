<?php

use App\Support\Settings;

it('devuelve el default cuando la clave no existe', fn () =>
    expect(setting('mission', 'x'))->toBe('x'));

it('guarda y lee un valor invalidando la cache', function () {
    setting('mission'); // calienta cache
    app(Settings::class)->set('mission', 'Nueva misión');
    expect(setting('mission'))->toBe('Nueva misión');
});

it('usa el logo de marca cuando no hay logo subido', fn () =>
    expect(app(Settings::class)->imageUrl('logo_path', 'images/brand/logo.png'))
        ->toEndWith('images/brand/logo.png'));

it('usa el logo subido cuando existe el archivo', function () {
    Storage::fake('public');
    Storage::disk('public')->put('branding/logo.png', 'x');
    app(Settings::class)->set('logo_path', 'branding/logo.png');
    expect(app(Settings::class)->imageUrl('logo_path', 'images/brand/logo.png'))
        ->toContain('branding/logo.png');
});

it('la home muestra el lema guardado', function () {
    app(Settings::class)->set('tagline', 'Lema de prueba');
    $this->get('/')->assertSee('Lema de prueba');
});

it('el seeder no pisa valores editados', function () {
    app(Settings::class)->set('tagline', 'Editado');
    $this->seed(\Database\Seeders\SettingsSeeder::class);
    expect(setting('tagline'))->toBe('Editado')
        ->and(setting('site_name'))->toBe('LyK Asesoría y Consultoría de Pensiones y AFORE');
});
