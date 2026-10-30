<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Support\ImageUploader;
use App\Support\Settings;

class SettingsController extends Controller
{
    private const IMAGES = ['logo' => 'logo_path', 'about_image' => 'about_image_path'];

    public function edit(Settings $settings)
    {
        return view('admin.settings.edit', ['settings' => $settings]);
    }

    public function update(UpdateSettingsRequest $request, Settings $settings)
    {
        $values = collect($request->validated())
            ->only(array_diff(Settings::KEYS, self::IMAGES))
            ->all();

        foreach (self::IMAGES as $field => $key) {
            if ($request->hasFile($field)) {
                $values[$key] = ImageUploader::replace($settings->get($key), $request->file($field), 'branding');
            }
        }

        $settings->setMany($values);

        return redirect()->route('admin.settings.edit')->with('status', 'Los ajustes se guardaron.');
    }
}
