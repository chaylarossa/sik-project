<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UrgencyLevel>
 */
class UrgencyLevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'level' => fake()->unique()->numberBetween(1, 5),
            'color' => fake()->safeHexColor(),
            'is_high_priority' => fake()->boolean(30),
        ];
    }
}
