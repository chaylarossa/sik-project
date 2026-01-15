<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'code' => strtoupper($this->faker->unique()->lexify('RGN???')),
            'level' => Region::LEVEL_PROVINCE,
            'parent_id' => null,
        ];
    }

    public function city(?Region $province = null): self
    {
        return $this->state(function () use ($province) {
            $province ??= Region::factory()->create(['level' => Region::LEVEL_PROVINCE]);

            return [
                'level' => Region::LEVEL_CITY,
                'parent_id' => $province->id,
                'name' => $this->faker->city(),
                'code' => strtoupper($this->faker->unique()->lexify('CITY??')),
            ];
        });
    }

    public function district(?Region $city = null): self
    {
        return $this->state(function () use ($city) {
            $city ??= Region::factory()->city()->create();

            return [
                'level' => Region::LEVEL_DISTRICT,
                'parent_id' => $city->id,
                'name' => $this->faker->citySuffix().' District',
                'code' => strtoupper($this->faker->unique()->lexify('DIST??')),
            ];
        });
    }

    public function village(?Region $district = null): self
    {
        return $this->state(function () use ($district) {
            $district ??= Region::factory()->district()->create();

            return [
                'level' => Region::LEVEL_VILLAGE,
                'parent_id' => $district->id,
                'name' => $this->faker->streetName(),
                'code' => strtoupper($this->faker->unique()->lexify('VLG???')),
            ];
        });
    }
}
