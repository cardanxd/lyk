<?php

namespace App\Http\Controllers\Admin;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Model;

class HeroSlideController extends OrderableController
{
    protected function model(): string { return HeroSlide::class; }

    protected function name(): string { return 'slides'; }

    protected function labels(): array { return ['Portadas', 'Portada']; }

    protected function imageField(): ?array { return ['image' => 'image_path']; }

    protected function imageRequiredOnCreate(): bool { return true; }

    protected function rules(?Model $item): array
    {
        return [
            'title' => ['nullable', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:250'],
            'button_text' => ['nullable', 'string', 'max:40', 'required_with:button_url'],
            'button_url' => ['nullable', 'max:500', 'regex:#^(https?://|/)#i'],
        ];
    }
}
