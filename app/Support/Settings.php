<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Settings
{
    public const CACHE_KEY = 'settings.all';

    public const KEYS = [
        'site_name', 'tagline', 'logo_path', 'mission', 'vision', 'values', 'history', 'about_image_path',
        'phone', 'whatsapp', 'email', 'address', 'schedule', 'map_embed_url',
        'facebook_url', 'instagram_url', 'tiktok_url', 'youtube_url',
        'privacy_notice', 'seo_description', 'notify_email',
    ];

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => Setting::pluck('value', 'key')->all());
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->all()[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    public function set(string $key, ?string $value): void
    {
        $this->setMany([$key => $value]);
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /** URL de una imagen guardada en ajustes, o del recurso de marca si no existe. */
    public function imageUrl(string $key, string $fallbackAsset): string
    {
        $path = $this->get($key);

        if ($path && Storage::disk('public')->exists($path)) {
            return storage_url($path);
        }

        return asset($fallbackAsset);
    }
}
