<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('storage_url')) {
    /** URL pública de un archivo del disco "public" (vía storage:link), con el host de la petición. */
    function storage_url(?string $path): ?string
    {
        return $path ? asset('storage/'.ltrim($path, '/')) : null;
    }
}
