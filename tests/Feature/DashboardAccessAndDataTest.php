<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessAndDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_authorized_roles_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $leader = User::factory()->create();
        $leader->assignRole(RoleName::Pimpinan->value);

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($leader)->get(route('dashboard'))->assertOk();
    }

    public function test_forbidden_role_gets_403(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
    }

    public function test_dashboard_shows_summary_counts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        CrisisReport::factory()->create(['status' => CrisisReport::STATUS_NEW]);
        CrisisReport::factory()->count(2)->create(['status' => CrisisReport::STATUS_IN_PROGRESS]);
        CrisisReport::factory()->create(['status' => CrisisReport::STATUS_DONE]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()->assertViewHas('cards', function ($cards) {
            return ($cards['pending_verification'] ?? 0) === 1
                && ($cards['in_progress'] ?? 0) === 2
                && ($cards['done_closed'] ?? 0) === 1
                && ($cards['active'] ?? 0) === 3;
        });
    }
}
