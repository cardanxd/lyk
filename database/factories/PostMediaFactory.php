<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'type' => 'image',
            'path' => 'posts/gallery/'.fake()->uuid().'.jpg',
        ];
    }

    public function video(): static
    {
        return $this->state(fn () => ['type' => 'video', 'path' => 'posts/videos/'.fake()->uuid().'.mp4']);
    }

    public function embed(): static
    {
        return $this->state(fn () => ['type' => 'embed', 'path' => null, 'url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ']);
    }
}
