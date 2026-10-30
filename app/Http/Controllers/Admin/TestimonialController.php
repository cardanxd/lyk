<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;

class TestimonialController extends OrderableController
{
    protected function model(): string { return Testimonial::class; }

    protected function name(): string { return 'testimonials'; }

    protected function labels(): array { return ['Testimonios', 'Testimonio']; }

    protected function imageField(): ?array { return ['photo' => 'photo_path']; }

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'quote' => ['required', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:100'],
        ];
    }
}
