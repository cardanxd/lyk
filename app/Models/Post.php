<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'cover_path', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug ??= static::uniqueSlug($post->title);
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('title') && $post->getOriginal('status') === 'draft') {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });

        static::saving(function (Post $post) {
            if ($post->status === 'published' && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;

        for ($i = 2; static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    /** Cuerpo adaptado al editor Trix, que sólo conoce <h1> como título. */
    public function editorBody(): string
    {
        return preg_replace('#<(/?)h[23]\b#i', '<$1h1', (string) $this->body);
    }

    public function excerptText(): string
    {
        return $this->excerpt ?: Str::limit(trim(html_entity_decode(strip_tags($this->body))), 160);
    }
}
