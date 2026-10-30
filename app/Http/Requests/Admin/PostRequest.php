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
        $data['body'] = clean($data['body'] ?? '', 'post');

        return $data;
    }
}
