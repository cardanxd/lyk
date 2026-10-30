<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HeroSlideFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image_path' => 'slides/'.fake()->uuid().'.jpg',
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(8),
        ];
    }
}
