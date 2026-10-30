<?php

use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

it('siembra contenido inicial sin duplicar al repetirse', function () {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Service::count())->toBe(7)
        ->and(Post::published()->count())->toBe(3)
        ->and(HeroSlide::count())->toBe(1)
        ->and(Faq::count())->toBeGreaterThanOrEqual(4)
        ->and(Testimonial::count())->toBe(0);

    Storage::disk('public')->assertExists(HeroSlide::first()->image_path);
    Post::all()->each(fn ($p) => Storage::disk('public')->assertExists($p->cover_path));
});

it('la home se ve completa con el contenido inicial', function () {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);
    $this->get('/')->assertOk()->assertSee(['Modalidad 40', 'Preguntas frecuentes', 'Infórmate antes de decidir']);
});
