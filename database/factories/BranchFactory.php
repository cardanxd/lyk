<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Sucursal '.fake()->unique()->city(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'phone' => fake()->numerify('55 #### ####'),
            'schedule' => 'Lunes a viernes de 9:00 a 18:00 h',
        ];
    }
}
