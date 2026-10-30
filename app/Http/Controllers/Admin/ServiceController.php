<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ServiceController extends OrderableController
{
    protected function model(): string { return Service::class; }

    protected function name(): string { return 'services'; }

    protected function labels(): array { return ['Servicios', 'Servicio']; }

    protected function rules(?Model $item): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:600'],
            'icon' => ['required', Rule::in(Service::ICONS)],
        ];
    }
}
