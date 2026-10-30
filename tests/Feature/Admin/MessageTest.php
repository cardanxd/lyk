<?php

use App\Models\ContactMessage;
use App\Models\MessageTag;
use App\Models\User;

beforeEach(fn () => $this->actingAs(User::factory()->create()));

function inbox(string $query = '')
{
    return test()->get('/admin/mensajes'.$query)->assertOk();
}

it('lista los mensajes con contadores por vista', function () {
    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->create(['name' => 'Ana López', 'read_at' => now(), 'status' => 'in_progress']);
    ContactMessage::factory()->create(['read_at' => now(), 'status' => 'done']);

    inbox()->assertSee('Ana López')
        ->assertViewHas('counts', fn ($c) => $c === [
            'todos' => 4, 'no-leidos' => 2, 'leidos' => 2, 'pendientes' => 2, 'en-proceso' => 1, 'atendidos' => 1,
        ]);
});

it('filtra por cada vista', function (string $vista, array $expected) {
    ContactMessage::factory()->create(['name' => 'Nuevo']);
    ContactMessage::factory()->create(['name' => 'Leído pendiente', 'read_at' => now()]);
    ContactMessage::factory()->create(['name' => 'En proceso', 'read_at' => now(), 'status' => 'in_progress']);
    ContactMessage::factory()->create(['name' => 'Cerrado', 'read_at' => now(), 'status' => 'done']);

    inbox("?vista=$vista")->assertViewHas('messages', fn ($m) => collect($m->items())->pluck('name')->sort()->values()->all() === collect($expected)->sort()->values()->all());
})->with([
    ['no-leidos', ['Nuevo']],
    ['leidos', ['Leído pendiente', 'En proceso', 'Cerrado']],
    ['pendientes', ['Nuevo', 'Leído pendiente']],
    ['en-proceso', ['En proceso']],
    ['atendidos', ['Cerrado']],
    ['xyz', ['Nuevo', 'Leído pendiente', 'En proceso', 'Cerrado']],
]);

it('filtra por etiqueta y combina con la vista', function () {
    $tag = MessageTag::factory()->create(['name' => 'Modalidad 40']);
    ContactMessage::factory()->create(['name' => 'Con etiqueta'])->tags()->attach($tag);
    ContactMessage::factory()->create(['name' => 'Con etiqueta leído', 'read_at' => now()])->tags()->attach($tag);
    ContactMessage::factory()->create(['name' => 'Sin etiqueta']);

    inbox("?etiqueta={$tag->id}")->assertSee(['Con etiqueta', 'Con etiqueta leído'])->assertDontSee('Sin etiqueta');
    inbox("?etiqueta={$tag->id}&vista=no-leidos")->assertSee('Con etiqueta')->assertDontSee('Con etiqueta leído');
});

it('una etiqueta inexistente muestra la bandeja vacía', function () {
    ContactMessage::factory()->create(['name' => 'Alguien']);
    inbox('?etiqueta=999')->assertDontSee('Alguien');
});

it('muestra el estado y las etiquetas de cada mensaje en la lista', function () {
    $tag = MessageTag::factory()->create(['name' => 'Urgente']);
    ContactMessage::factory()->create(['status' => 'in_progress'])->tags()->attach($tag);
    inbox()->assertSee('En proceso')->assertSee('Urgente');
});

it('abrir un mensaje lo marca como leído', function () {
    $m = ContactMessage::factory()->create();
    $this->get("/admin/mensajes/{$m->id}")->assertOk()->assertSee($m->message);
    expect($m->fresh()->read_at)->not->toBeNull();
});

it('marca como no leído y de nuevo como leído', function () {
    $m = ContactMessage::factory()->create(['read_at' => now()]);
    $this->patch("/admin/mensajes/{$m->id}/leido")->assertRedirect();
    expect($m->fresh()->read_at)->toBeNull();
    $this->patch("/admin/mensajes/{$m->id}/leido");
    expect($m->fresh()->read_at)->not->toBeNull();
});

it('cambia el estado y las etiquetas de un mensaje', function () {
    $a = MessageTag::factory()->create();
    $b = MessageTag::factory()->create();
    $m = ContactMessage::factory()->create();
    $m->tags()->attach($a);

    $this->patch("/admin/mensajes/{$m->id}", ['status' => 'in_progress', 'tags' => [$b->id]])
        ->assertRedirect()->assertSessionHasNoErrors();
    expect($m->fresh()->status)->toBe('in_progress')
        ->and($m->fresh()->tags->pluck('id')->all())->toBe([$b->id]);

    $this->patch("/admin/mensajes/{$m->id}", ['status' => 'done']);
    expect($m->fresh()->tags)->toHaveCount(0);
});

it('rechaza estados y etiquetas inexistentes', function () {
    $m = ContactMessage::factory()->create();
    $this->patch("/admin/mensajes/{$m->id}", ['status' => 'borrado', 'tags' => [999]])
        ->assertSessionHasErrors(['status', 'tags.0']);
    expect($m->fresh()->status)->toBe('pending');
});

it('el detalle muestra selector de estado y casillas de etiquetas', function () {
    MessageTag::factory()->create(['name' => 'AFORE']);
    $m = ContactMessage::factory()->create();
    $this->get("/admin/mensajes/{$m->id}")->assertSee(['Pendiente', 'En proceso', 'Atendido', 'AFORE', 'Marcar como no leído']);
});

it('borra un mensaje', function () {
    $m = ContactMessage::factory()->create();
    $this->delete("/admin/mensajes/{$m->id}")->assertRedirect('/admin/mensajes');
    expect(ContactMessage::count())->toBe(0);
});
