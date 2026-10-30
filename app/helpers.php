<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        return app(Settings::class)->get($key, $default);
    }
}
