<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class BranchController extends OrderableController
{
    protected function model(): string { return Branch::class; }

    protected function name(): string { return 'branches'; }

    protected function labels(): array { return ['Sucursales', 'Sucursal']; }

    protected function imageField(): ?array { return ['photo' => 'photo_path']; }

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:250'],
            'city' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'regex:/^\+?\d{10,13}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'schedule' => ['nullable', 'string', 'max:200'],
            'maps_url' => ['nullable', 'url', 'max:500'],
            'map_embed_url' => ['nullable', 'url', 'max:1000'],
        ];
    }

    protected function attributeNames(): array
    {
        return parent::attributeNames() + [
            'address' => 'dirección', 'city' => 'ciudad', 'phone' => 'teléfono', 'whatsapp' => 'WhatsApp',
            'email' => 'correo', 'schedule' => 'horario', 'maps_url' => 'enlace de Google Maps',
            'map_embed_url' => 'URL del mapa insertado',
        ];
    }
}
