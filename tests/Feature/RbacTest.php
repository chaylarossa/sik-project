<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }

    public function test_admin_can_manage_master_data(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($user)->get('/admin/master-data');

        $response->assertOk();
    }

    public function test_verifikator_cannot_manage_master_data(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get('/admin/master-data');

        $response->assertForbidden();
    }
}
