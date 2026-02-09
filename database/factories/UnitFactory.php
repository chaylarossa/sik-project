<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'name' => 'Unit '.$this->faker->unique()->word(),
            'code' => strtoupper($this->faker->unique()->bothify('UNT-###')),
            'contact_phone' => $this->faker->optional()->numerify('08##########'),
            'is_active' => true,
        ];
    }
}
