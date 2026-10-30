<?php

namespace App\Http\Requests\Admin;

use App\Support\ImageUploader;
use App\Support\VideoEmbed;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public const MAX_IMAGES = 10;

    public const MAX_VIDEOS = 3;

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
            'gallery_images' => ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'gallery_images.*' => ImageUploader::RULES,
            'gallery_videos' => ['nullable', 'array', 'max:'.self::MAX_VIDEOS],
            'gallery_videos.*' => ImageUploader::VIDEO_RULES,
            'video_url' => ['nullable', 'string', 'max:2000', function (string $attribute, mixed $value, Closure $fail) {
                if (VideoEmbed::isFacebookShareLink((string) $value)) {
                    $fail(VideoEmbed::SHARE_LINK);
                } elseif (! VideoEmbed::fromUrl((string) $value)) {
                    $fail('Pega un enlace de un video de YouTube o Facebook.');
                }
            }],
            'media' => ['nullable', 'array'],
            'media.*.caption' => ['nullable', 'string', 'max:200'],
            'media.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título', 'excerpt' => 'extracto', 'body' => 'contenido',
            'status' => 'estado', 'published_at' => 'fecha de publicación', 'cover' => 'imagen de portada',
            'gallery_images' => 'fotos', 'gallery_images.*' => 'foto', 'gallery_videos' => 'videos',
            'gallery_videos.*' => 'video', 'video_url' => 'enlace del video', 'media.*.caption' => 'descripción',
        ];
    }

    public function messages(): array
    {
        return ImageUploader::messages('cover', 'gallery_images.*') + [
            'gallery_images.max' => 'Puedes subir hasta '.self::MAX_IMAGES.' fotos a la vez.',
            'gallery_videos.max' => 'Puedes subir hasta '.self::MAX_VIDEOS.' videos a la vez.',
            'gallery_videos.*.max' => ImageUploader::VIDEO_TOO_LARGE,
            'gallery_videos.*.uploaded' => ImageUploader::VIDEO_TOO_LARGE,
            'gallery_videos.*.mimetypes' => 'Los videos deben ser MP4, MOV o WEBM.',
        ];
    }

    /** Datos del post listos para guardar: cuerpo sanitizado, sin archivos ni galería. */
    public function postData(): array
    {
        $data = collect($this->validated())
            ->except(['cover', 'gallery_images', 'gallery_videos', 'video_url', 'media'])
            ->all();
        // Trix sólo genera <h1>; en el sitio el h1 es el título del post, así que se guardan como <h2>.
        $body = preg_replace('#<(/?)h1\b#i', '<$1h2', $data['body'] ?? '');
        $data['body'] = clean($body, 'post');

        return $data;
    }
}
