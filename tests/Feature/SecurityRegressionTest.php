<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\UrgencyLevel;
use App\Models\Region;
use App\Enums\RoleName;
use App\Enums\PermissionName;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        $this->seed([RbacSeeder::class]);
        
        // Factories for required masters
        CrisisType::factory()->create();
        UrgencyLevel::factory()->create();
    }

    /** @test */
    public function unauthenticated_users_cannot_access_sensitive_endpoints()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        // Should use GET as defined in api.php
        $response = $this->getJson('/api/internal/maps/crisis-points');
        // Expect 401 because it's protected by auth:sanctum
        $response->assertStatus(401);
    }

    /** @test */
    public function unauthorized_role_cannot_access_dashboard()
    {
        // Public/random user without roles
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function rate_limit_is_applied_to_internal_api()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator);

        // Hit the endpoint 61 times
        for ($i = 0; $i < 61; $i++) {
            $response = $this->actingAs($user)->getJson('/api/internal/dashboard/summary');
        }

        // The 61st request should be blocked
        $response->assertStatus(429);
    }

    /** @test */
    public function store_report_validation_check()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan); // Can create

        $response = $this->actingAs($user)->postJson(route('reports.store'), [
            // Missing all required fields
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['crisis_type_id', 'urgency_level_id', 'latitude', 'longitude']);
            
        // Test invalid lat/lng
        $response = $this->actingAs($user)->postJson(route('reports.store'), [
            'latitude' => 91, // Invalid > 90
            'longitude' => 181, // Invalid > 180
        ]);
        
        $response->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    /** @test */
    public function invalid_role_assignment_blocked()
    {
        // Example logic: ensure normal user cannot assign bad permissions or roles
        // Ideally checking policies
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/master-data');
        $response->assertStatus(403);
    }
}
