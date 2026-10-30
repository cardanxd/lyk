<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    public const RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

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
