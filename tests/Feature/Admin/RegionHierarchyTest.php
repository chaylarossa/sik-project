<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_child_region_with_valid_parent(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $province = Region::factory()->create([
            'level' => Region::LEVEL_PROVINCE,
            'parent_id' => null,
            'code' => 'PRN1',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.regions.store'), [
            'name' => 'Kota Uji',
            'code' => 'KTUJ',
            'level' => Region::LEVEL_CITY,
            'parent_id' => $province->id,
        ]);

        $response->assertRedirect(route('admin.regions.index'));

        $this->assertDatabaseHas('regions', [
            'name' => 'Kota Uji',
            'code' => 'KTUJ',
            'level' => Region::LEVEL_CITY,
            'parent_id' => $province->id,
        ]);
    }

    public function test_validation_fails_when_parent_missing_for_child(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($admin)->post(route('admin.regions.store'), [
            'name' => 'Wilayah Salah',
            'code' => 'WSAL',
            'level' => Region::LEVEL_DISTRICT,
            'parent_id' => 9999,
        ]);

        $response->assertSessionHasErrors(['parent_id']);
        $this->assertDatabaseMissing('regions', [
            'code' => 'WSAL',
        ]);
    }

    public function test_non_admin_cannot_create_region(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->post(route('admin.regions.store'), [
            'name' => 'Tidak Boleh',
            'code' => 'NADM',
            'level' => Region::LEVEL_PROVINCE,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('regions', [
            'code' => 'NADM',
        ]);
    }
}
