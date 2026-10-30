<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->numerify('55########'),
            'email' => fake()->safeEmail(),
            'topic' => fake()->randomElement(ContactMessage::TOPICS),
            'message' => fake()->paragraph(),
        ];
    }
}
