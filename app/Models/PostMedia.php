<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'post_media';

    protected $fillable = ['type', 'path', 'url', 'caption', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /** URL para mostrar: el archivo subido o la URL de inserción del video. */
    public function publicUrl(): string
    {
        return $this->type === 'embed' ? (string) $this->url : (string) storage_url($this->path);
    }
}
