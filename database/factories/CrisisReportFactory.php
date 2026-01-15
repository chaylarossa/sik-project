<?php

namespace Database\Factories;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrisisReport>
 */
class CrisisReportFactory extends Factory
{
    protected $model = CrisisReport::class;

    public function definition(): array
    {
        $hasCoordinates = fake()->boolean(70);

        return [
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => fake()->numberBetween(1, 10),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'description' => fake()->paragraphs(2, true),
            'latitude' => $hasCoordinates ? fake()->latitude() : null,
            'longitude' => $hasCoordinates ? fake()->longitude() : null,
            'address_text' => fake()->address(),
            'created_by' => User::factory(),
        ];
    }
}
