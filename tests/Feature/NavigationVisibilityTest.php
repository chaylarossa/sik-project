<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_access_handling_page(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan->value);

        $response = $this->actingAs($user)->get('/handling');

        $response->assertOk();
    }

    public function test_publik_cannot_access_handling_page(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);

        $response = $this->actingAs($user)->get('/handling');

        $response->assertForbidden();
    }

    public function test_verifikator_can_access_verification_page(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get('/verifications');

        $response->assertOk();
    }
}
