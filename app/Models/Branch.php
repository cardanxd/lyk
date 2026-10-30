<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory, Orderable;

    protected $fillable = [
        'name', 'address', 'city', 'phone', 'whatsapp', 'email', 'schedule',
        'maps_url', 'map_embed_url', 'photo_path', 'sort_order', 'is_active',
    ];
}
