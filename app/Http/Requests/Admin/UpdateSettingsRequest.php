<?php

namespace App\Http\Requests\Admin;

use App\Support\ImageUploader;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $longText = ['nullable', 'string', 'max:5000'];
        $url = ['nullable', 'url', 'max:500'];

        return [
            'site_name' => ['required', 'string', 'max:150'],
            'tagline' => ['required', 'string', 'max:200'],
            'mission' => $longText,
            'vision' => $longText,
            'values' => $longText,
            'history' => $longText,
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'regex:/^\+?\d{10,13}$/'],
            'email' => ['required', 'email', 'max:150'],
            'notify_email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:300'],
            'schedule' => ['nullable', 'string', 'max:200'],
            'map_embed_url' => $url,
            'facebook_url' => $url,
            'instagram_url' => $url,
            'tiktok_url' => $url,
            'youtube_url' => $url,
            'privacy_notice' => ['nullable', 'string', 'max:20000'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'logo' => ['nullable', ...ImageUploader::RULES],
            'about_image' => ['nullable', ...ImageUploader::RULES],
        ];
    }

    public function attributes(): array
    {
        return [
            'site_name' => 'nombre del sitio', 'tagline' => 'lema', 'mission' => 'misión', 'vision' => 'visión',
            'values' => 'valores', 'history' => 'historia', 'phone' => 'teléfono', 'email' => 'correo',
            'notify_email' => 'correo para avisos', 'address' => 'dirección', 'schedule' => 'horario',
            'map_embed_url' => 'URL del mapa', 'facebook_url' => 'Facebook', 'instagram_url' => 'Instagram',
            'tiktok_url' => 'TikTok', 'youtube_url' => 'YouTube', 'privacy_notice' => 'aviso de privacidad',
            'seo_description' => 'descripción para buscadores', 'logo' => 'logo', 'about_image' => 'foto de nosotros',
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Escribe el WhatsApp sólo con números, con lada, por ejemplo +525512345678.',
        ];
    }
}
