<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrisisTypeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_crisis_type(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.crisis-types.store'), [
            'name' => 'Kebakaran',
            'code' => 'FIRE',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.crisis-types.index'));

        $this->assertDatabaseHas('crisis_types', [
            'name' => 'Kebakaran',
            'code' => 'FIRE',
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_access_crisis_types(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get(route('admin.crisis-types.index'));

        $response->assertForbidden();
    }

    public function test_validation_fails_when_name_is_empty(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.crisis-types.store'), [
            'name' => '',
            'code' => 'FIRE',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing('crisis_types', [
            'code' => 'FIRE',
        ]);
    }
}
