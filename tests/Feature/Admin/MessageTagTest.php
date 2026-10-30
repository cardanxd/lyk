<?php

use App\Models\ContactMessage;
use App\Models\MessageTag;
use App\Models\User;

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('lista las etiquetas con cuántos mensajes tiene cada una', function () {
    $tag = MessageTag::factory()->create(['name' => 'Modalidad 40']);
    ContactMessage::factory()->count(2)->create()->each(fn ($m) => $m->tags()->attach($tag));
    $this->get('/admin/etiquetas')->assertOk()->assertSee('Modalidad 40')->assertSee('2 mensajes');
});

it('crea una etiqueta', function () {
    $this->post('/admin/etiquetas', ['name' => 'Urgente', 'color' => 'red'])->assertRedirect('/admin/etiquetas');
    expect(MessageTag::first())->name->toBe('Urgente')->color->toBe('red');
});

it('rechaza nombre duplicado sin importar mayúsculas y color inválido', function () {
    MessageTag::factory()->create(['name' => 'Urgente']);
    $this->post('/admin/etiquetas', ['name' => 'urgente', 'color' => 'pink'])
        ->assertSessionHasErrors(['name', 'color']);
    expect(session('errors')->first('name'))->toContain('Ya existe');
});

it('edita una etiqueta conservando su nombre', function () {
    $tag = MessageTag::factory()->create(['name' => 'AFORE', 'color' => 'gray']);
    $this->put("/admin/etiquetas/{$tag->id}", ['name' => 'AFORE', 'color' => 'blue'])->assertSessionHasNoErrors();
    expect($tag->fresh()->color)->toBe('blue');
});

it('borrar una etiqueta la quita de los mensajes sin borrarlos', function () {
    $tag = MessageTag::factory()->create();
    $m = ContactMessage::factory()->create();
    $m->tags()->attach($tag);
    $this->delete("/admin/etiquetas/{$tag->id}")->assertRedirect('/admin/etiquetas');
    expect(MessageTag::count())->toBe(0)->and($m->fresh()->tags)->toHaveCount(0)->and(ContactMessage::count())->toBe(1);
});

it('muestra el formulario de alta y edición', function () {
    $tag = MessageTag::factory()->create();
    $this->get('/admin/etiquetas/nuevo')->assertOk();
    $this->get("/admin/etiquetas/{$tag->id}/editar")->assertOk();
});

it('un mensaje nuevo del formulario público nace pendiente', function () {
    auth()->logout();
    Illuminate\Support\Facades\Mail::fake();
    $this->post('/contacto', validContact());
    expect(ContactMessage::first()->status)->toBe('pending');
});
