<?php

namespace Database\Factories;

use App\Models\MessageTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'color' => fake()->randomElement(array_keys(MessageTag::COLORS)),
        ];
    }
}
