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

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_administrator_can_access_admin_master_data(): void
    {
        $user = $this->createUserWithRole(RoleName::Administrator);

        $this->actingAs($user)
            ->get('/admin/master-data')
            ->assertOk();
    }

    public function test_operator_is_forbidden_from_admin_master_data(): void
    {
        $user = $this->createUserWithRole(RoleName::OperatorLapangan);

        $this->actingAs($user)
            ->get('/admin/master-data')
            ->assertForbidden();
    }

    public function test_verifikator_can_access_verifications_but_operator_cannot(): void
    {
        $verifikator = $this->createUserWithRole(RoleName::Verifikator);
        $operator = $this->createUserWithRole(RoleName::OperatorLapangan);

        $this->actingAs($verifikator)
            ->get('/verifications')
            ->assertOk();

        $this->actingAs($operator)
            ->get('/verifications')
            ->assertForbidden();
    }

    private function createUserWithRole(RoleName $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user;
    }
}
