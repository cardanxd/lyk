<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, Orderable;

    public const ICONS = ['piggy-bank', 'document', 'calendar', 'shield', 'users', 'scale', 'heart'];

    protected $fillable = ['title', 'description', 'icon', 'sort_order', 'is_active'];
}
