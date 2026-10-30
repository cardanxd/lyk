<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;

class FaqController extends OrderableController
{
    protected function model(): string { return Faq::class; }

    protected function name(): string { return 'faqs'; }

    protected function labels(): array { return ['Preguntas frecuentes', 'Pregunta']; }

    protected function rules(?Model $item): array
    {
        return [
            'question' => ['required', 'string', 'max:250'],
            'answer' => ['required', 'string', 'max:3000'],
        ];
    }
}
