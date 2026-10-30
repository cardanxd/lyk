<?php

use App\Models\ContactMessage;
use App\Models\User;

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('lista los mensajes y cuenta los no leídos en el menú', function () {
    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->create(['name' => 'Ana López', 'read_at' => now()]);
    $this->get('/admin/mensajes')->assertOk()->assertSee('Ana López')->assertSee('2 sin leer');
});

it('abrir un mensaje lo marca como leído', function () {
    $m = ContactMessage::factory()->create();
    $this->get("/admin/mensajes/{$m->id}")->assertOk()->assertSee($m->message);
    expect($m->fresh()->read_at)->not->toBeNull();
});

it('marca y desmarca como atendido', function () {
    $m = ContactMessage::factory()->create(['read_at' => now()]);
    $this->patch("/admin/mensajes/{$m->id}/atendido")->assertRedirect();
    expect($m->fresh()->read_at)->toBeNull();
    $this->patch("/admin/mensajes/{$m->id}/atendido");
    expect($m->fresh()->read_at)->not->toBeNull();
});

it('borra un mensaje', function () {
    $m = ContactMessage::factory()->create();
    $this->delete("/admin/mensajes/{$m->id}")->assertRedirect('/admin/mensajes');
    expect(ContactMessage::count())->toBe(0);
});
