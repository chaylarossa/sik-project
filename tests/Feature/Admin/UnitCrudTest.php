<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_unit(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.units.store'), [
            'name' => 'BPBD',
            'code' => 'bpbd',
            'contact_phone' => '021-999999',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.units.index'));

        $this->assertDatabaseHas('units', [
            'name' => 'BPBD',
            'code' => 'BPBD',
            'contact_phone' => '021-999999',
            'is_active' => true,
        ]);
    }

    public function test_unit_code_must_be_unique(): void
    {
        $this->seed(RbacSeeder::class);

        Unit::factory()->create([
            'code' => 'DINKES',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.units.store'), [
            'name' => 'Dinas Kesehatan',
            'code' => 'DINKES',
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_non_admin_cannot_access_units(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get(route('admin.units.index'));

        $response->assertForbidden();
    }
}
