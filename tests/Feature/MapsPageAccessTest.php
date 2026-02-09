<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapsPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_role_can_access_maps_page(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($user)->get(route('maps.index'));

        $response->assertOk();
    }

    public function test_unauthorized_role_cannot_access_maps_page(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);

        $response = $this->actingAs($user)->get(route('maps.index'));

        $response->assertForbidden();
    }
}
