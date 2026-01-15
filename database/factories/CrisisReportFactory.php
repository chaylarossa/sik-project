<?php

namespace Database\Factories;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
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
        return [
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
            'created_by' => User::factory(),
            'status' => CrisisReport::STATUS_NEW,
            'occurred_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'address' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(-9.5, 6.0),
            'longitude' => $this->faker->longitude(95.0, 141.0),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function withStatus(string $status): self
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
