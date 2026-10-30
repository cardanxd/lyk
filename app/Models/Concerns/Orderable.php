<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** Contenido con orden manual (sort_order) y activo/inactivo (is_active). */
trait Orderable
{
    public function initializeOrderable(): void
    {
        $this->mergeCasts(['is_active' => 'boolean', 'sort_order' => 'integer']);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
