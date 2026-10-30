<?php

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Support\Settings;
use Illuminate\Support\Facades\Mail;

it('muestra la página de contacto con los datos configurados', function () {
    app(Settings::class)->setMany(['phone' => '55 1234 5678', 'email' => 'hola@lyk.mx', 'schedule' => 'Lunes a viernes']);
    $this->get('/contacto')->assertOk()->assertSee(['55 1234 5678', 'hola@lyk.mx', 'Lunes a viernes', 'Aviso de privacidad']);
});

it('muestra el mapa sólo si hay URL', function () {
    $this->get('/contacto')->assertDontSee('<iframe', false);
    app(Settings::class)->set('map_embed_url', 'https://www.google.com/maps/embed?pb=abc');
    $this->get('/contacto')->assertSee('https://www.google.com/maps/embed?pb=abc', false);
});

it('guarda el mensaje y avisa por correo', function () {
    Mail::fake();
    app(Settings::class)->set('notify_email', 'dueno@lyk.mx');
    $this->post('/contacto', validContact())->assertRedirect('/contacto')->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(1)
        ->and(ContactMessage::first()->topic)->toBe('Modalidad 40');
    Mail::assertSent(NewContactMessage::class, fn ($m) => $m->hasTo('dueno@lyk.mx'));
});

it('usa el correo de contacto si no hay correo de avisos', function () {
    Mail::fake();
    app(Settings::class)->set('email', 'hola@lyk.mx');
    $this->post('/contacto', validContact());
    Mail::assertSent(NewContactMessage::class, fn ($m) => $m->hasTo('hola@lyk.mx'));
});

it('si el correo falla el mensaje queda guardado', function () {
    app(Settings::class)->set('email', 'hola@lyk.mx');
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp'));
    $this->post('/contacto', validContact())->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(1);
});

it('ignora en silencio el honeypot', function () {
    $this->post('/contacto', validContact(['website' => 'spam.com']))->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(0);
});

it('exige aceptar el aviso de privacidad', fn () =>
    $this->post('/contacto', validContact(['privacy' => null]))->assertSessionHasErrors('privacy'));

it('valida teléfono y mensaje', fn () =>
    $this->post('/contacto', validContact(['phone' => '123', 'message' => '']))->assertSessionHasErrors(['phone', 'message']));

it('limita a 5 envíos por minuto', function () {
    Mail::fake();
    foreach (range(1, 5) as $_) {
        $this->post('/contacto', validContact());
    }
    $this->post('/contacto', validContact())->assertStatus(429)->assertSee('Demasiados');
});
