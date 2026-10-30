<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory, Orderable;

    protected $fillable = ['image_path', 'title', 'subtitle', 'button_text', 'button_url', 'sort_order', 'is_active'];
}
