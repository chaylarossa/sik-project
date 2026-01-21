<?php

namespace Database\Factories;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CrisisReport>
 */
class CrisisReportFactory extends Factory
{
    protected $model = CrisisReport::class;

    public function definition(): array
    {
        return [
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->district()->create()->id,
            'created_by' => User::factory(),
            'status' => CrisisReport::STATUS_NEW,
            'verification_status' => CrisisReport::VERIFICATION_PENDING,
            'occurred_at' => $this->faker->dateTimeBetween('-3 days', 'now'),
            'address' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(-7.5, -5.0),
            'longitude' => $this->faker->longitude(105.0, 110.0),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function withStatus(string $status): self
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function withVerificationStatus(string $status): self
    {
        return $this->state(fn () => ['verification_status' => $status]);
    }
}
