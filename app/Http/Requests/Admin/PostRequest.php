<?php

namespace App\Http\Requests\Admin;

use App\Support\ImageUploader;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['nullable', 'string', 'max:200000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', ...ImageUploader::RULES],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título', 'excerpt' => 'extracto', 'body' => 'contenido',
            'status' => 'estado', 'published_at' => 'fecha de publicación', 'cover' => 'imagen de portada',
        ];
    }

    /** Datos listos para guardar: cuerpo sanitizado, sin el archivo. */
    public function postData(): array
    {
        $data = collect($this->validated())->except('cover')->all();
        // Trix sólo genera <h1>; en el sitio el h1 es el título del post, así que se guardan como <h2>.
        $body = preg_replace('#<(/?)h1\b#i', '<$1h2', $data['body'] ?? '');
        $data['body'] = clean($body, 'post');

        return $data;
    }
}
