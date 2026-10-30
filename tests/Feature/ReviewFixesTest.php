<?php

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('un envío más grande que post_max_size muestra una página en español para volver', function () {
    $this->actingAs(User::factory()->create());
    $this->call('POST', '/admin/posts', [], [], [], ['CONTENT_LENGTH' => 50 * 1024 * 1024, 'CONTENT_TYPE' => 'multipart/form-data'])
        ->assertStatus(413)
        ->assertSee('máximo 5 MB')
        ->assertSee('history.back()', false);
});

it('una imagen que supera el límite de PHP muestra el límite de 5 MB', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $file = new UploadedFile(__FILE__, 'foto.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);
    $this->put('/admin/ajustes', validSettings(['logo' => $file]))->assertSessionHasErrors('logo');
    expect(session('errors')->first('logo'))->toContain('5 MB');
});

it('el correo SMTP tiene un tiempo de espera corto', fn () =>
    expect(config('mail.mailers.smtp.timeout'))->toBe(10));

it('el correo al dueño no convierte el texto del visitante en enlaces', function () {
    $m = ContactMessage::factory()->create(['message' => '[Verifica tu cuenta](https://phish.example)']);
    $html = (new NewContactMessage($m))->render();
    expect($html)->not->toContain('href="https://phish.example"');
});

it('volver a sembrar no restaura servicios renombrados', function () {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    Service::where('title', 'Modalidad 40')->update(['title' => 'Modalidad 40 (M40)']);
    $this->seed(DatabaseSeeder::class);
    expect(Service::count())->toBe(7);
});
