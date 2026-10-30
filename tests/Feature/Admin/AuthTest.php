<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('redirige a invitados al login', fn () =>
    $this->get('/admin')->assertRedirect('/admin/login'));

it('muestra el formulario de login', fn () =>
    $this->get('/admin/login')->assertOk()->assertSee('Iniciar sesión'));

it('inicia sesión con credenciales válidas', function () {
    $u = User::factory()->create(['password' => 'secreto123']);
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'secreto123'])
        ->assertRedirect('/admin');
    $this->assertAuthenticatedAs($u);
});

it('rechaza contraseña incorrecta', function () {
    $u = User::factory()->create();
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('bloquea al sexto intento en un minuto', function () {
    $this->freezeTime();
    $u = User::factory()->create();
    foreach (range(1, 5) as $_) {
        $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal']);
    }
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal'])
        ->assertSessionHasErrors(['email' => 'Demasiados intentos. Intenta de nuevo en 60 segundos.']);
});

it('cierra sesión', function () {
    $this->actingAs(User::factory()->create())->post('/admin/logout')->assertRedirect('/admin/login');
    $this->assertGuest();
});

it('muestra el panel al administrador', fn () =>
    $this->actingAs(User::factory()->create())->get('/admin')->assertOk()->assertSee('Panel'));

it('no existe registro público', fn () => $this->get('/register')->assertNotFound());

it('cambia la contraseña verificando la actual', function () {
    $u = User::factory()->create(['password' => 'secreto123']);
    $this->actingAs($u)->put('/admin/contrasena', [
        'current_password' => 'secreto123', 'password' => 'nueva12345', 'password_confirmation' => 'nueva12345',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('nueva12345', $u->fresh()->password))->toBeTrue();
});

it('rechaza el cambio si la contraseña actual es incorrecta', function () {
    $u = User::factory()->create(['password' => 'secreto123']);
    $this->actingAs($u)->put('/admin/contrasena', [
        'current_password' => 'otra', 'password' => 'nueva12345', 'password_confirmation' => 'nueva12345',
    ])->assertSessionHasErrors('current_password');
});

it('crea un admin por comando', function () {
    $this->artisan('lyk:admin', ['email' => 'a@lyk.mx'])
        ->expectsQuestion('Contraseña', 'secreto123')->assertSuccessful();
    expect(User::where('email', 'a@lyk.mx')->exists())->toBeTrue();
});
