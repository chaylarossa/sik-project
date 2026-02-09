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

class DashboardSummaryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_get_dashboard_summary(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);
        Sanctum::actingAs($user);

        CrisisReport::factory()->create([
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
        ]);

        $response = $this->getJson('/api/internal/dashboard/summary');

        $response
            ->assertOk()
            ->assertJsonStructure(['range', 'stats', 'charts']);
    }

    public function test_unauthenticated_user_cannot_get_dashboard_summary(): void
    {
        $response = $this->getJson('/api/internal/dashboard/summary');

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_get_dashboard_summary(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/internal/dashboard/summary');

        $response->assertForbidden();
    }

    public function test_invalid_date_filter_returns_validation_error(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/internal/dashboard/summary?date_from=2026-01-10&date_to=2026-01-01');

        $response->assertStatus(422);
    }
}
