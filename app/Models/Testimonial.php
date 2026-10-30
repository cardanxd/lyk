<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, Orderable;

    protected $fillable = ['name', 'quote', 'location', 'photo_path', 'sort_order', 'is_active'];
}
