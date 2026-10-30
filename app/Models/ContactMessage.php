<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactMessage extends Model
{
    use HasFactory;

    public const TOPICS = ['Modalidad 40', 'Pensión Ley 73', 'Pensión Ley 97', 'AFORE', 'Otro'];

    public const STATUSES = ['pending' => 'Pendiente', 'in_progress' => 'En proceso', 'done' => 'Atendido'];

    /** Vistas de la bandeja: slug => nombre visible (el filtro está en scopeInView). */
    public const VIEWS = [
        'todos' => 'Todos',
        'no-leidos' => 'No leídos',
        'leidos' => 'Leídos',
        'pendientes' => 'Pendientes',
        'en-proceso' => 'En proceso',
        'atendidos' => 'Atendidos',
    ];

    protected $fillable = ['name', 'phone', 'email', 'topic', 'message', 'read_at', 'status'];

    protected $attributes = ['status' => 'pending'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(MessageTag::class)->orderBy('name');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /** Aplica una vista de la bandeja; un slug desconocido no filtra. */
    public function scopeInView(Builder $query, string $view): Builder
    {
        return match ($view) {
            'no-leidos' => $query->whereNull('read_at'),
            'leidos' => $query->whereNotNull('read_at'),
            'pendientes' => $query->where('status', 'pending'),
            'en-proceso' => $query->where('status', 'in_progress'),
            'atendidos' => $query->where('status', 'done'),
            default => $query,
        };
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
