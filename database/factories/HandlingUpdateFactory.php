<?php

namespace Database\Factories;

use App\Models\CrisisReport;
use App\Models\HandlingUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\HandlingUpdate>
 */
class HandlingUpdateFactory extends Factory
{
    protected $model = HandlingUpdate::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(CrisisReport::STATUSES);

        return [
            'report_id' => CrisisReport::factory(),
            'updated_by' => User::factory(),
            'status' => $status,
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'note' => $this->faker->sentence(),
            'occurred_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
        ];
    }

    public function withStatus(string $status): self
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
