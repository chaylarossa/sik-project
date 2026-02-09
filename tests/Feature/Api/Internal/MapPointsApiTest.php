<?php

namespace Tests\Feature\Api\Internal;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MapPointsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_get_map_points(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);
        Sanctum::actingAs($user);

        $report = CrisisReport::factory()->create([
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
        ]);

        $response = $this->getJson('/api/internal/maps/crisis-points');

        $response
            ->assertOk()
            ->assertJsonStructure(['data', 'meta'])
            ->assertJsonFragment(['id' => $report->id]);
    }

    public function test_unauthenticated_user_cannot_get_map_points(): void
    {
        $response = $this->getJson('/api/internal/maps/crisis-points');

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_get_map_points(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/internal/maps/crisis-points');

        $response->assertForbidden();
    }

    public function test_invalid_filter_returns_validation_error(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/internal/maps/crisis-points?handling_status=INVALID');

        $response->assertStatus(422);
    }
}
