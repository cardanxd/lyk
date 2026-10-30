<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    public const RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

    public const VIDEO_RULES = ['file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200'];

    public const VIDEO_TOO_LARGE = 'Cada video debe pesar máximo 50 MB.';

    public const TOO_LARGE = 'El archivo es demasiado pesado. Usa imágenes JPG, PNG o WEBP de máximo 5 MB.';

    /** Mensajes de validación para campos de imagen (incluye el fallo de subida de PHP). */
    public static function messages(string ...$fields): array
    {
        $messages = [];
        foreach ($fields as $field) {
            $messages["$field.uploaded"] = self::TOO_LARGE;
            $messages["$field.max"] = self::TOO_LARGE;
        }

        return $messages;
    }

    public static function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    public static function replace(?string $oldPath, UploadedFile $file, string $folder): string
    {
        $path = self::store($file, $folder);
        self::delete($oldPath);

        return $path;
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
