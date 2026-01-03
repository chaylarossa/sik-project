<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\UrgencyLevel;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrgencyLevelCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_urgency_level(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.urgency-levels.store'), [
            'name' => 'Darurat',
            'level' => 5,
            'color' => '#FF0000',
            'is_high_priority' => true,
        ]);

        $response->assertRedirect(route('admin.urgency-levels.index'));

        $this->assertDatabaseHas('urgency_levels', [
            'name' => 'Darurat',
            'level' => 5,
            'color' => '#FF0000',
            'is_high_priority' => true,
        ]);
    }

    public function test_non_admin_cannot_access_urgency_levels(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get(route('admin.urgency-levels.index'));

        $response->assertForbidden();
    }

    public function test_level_validation_out_of_range_fails(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.urgency-levels.store'), [
            'name' => 'Tidak Valid',
            'level' => 6,
            'color' => null,
        ]);

        $response->assertSessionHasErrors(['level']);
        $this->assertDatabaseMissing('urgency_levels', [
            'name' => 'Tidak Valid',
        ]);
    }
}
