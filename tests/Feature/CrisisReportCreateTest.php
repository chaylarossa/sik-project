<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrisisReportCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_create_report(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan->value);

        $crisisType = CrisisType::factory()->create();
        $urgencyLevel = UrgencyLevel::factory()->create();
        $region = Region::factory()->village()->create();

        $payload = [
            'crisis_type_id' => $crisisType->id,
            'urgency_level_id' => $urgencyLevel->id,
            'region_id' => $region->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'address' => 'Jl. Uji Coba No. 1',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'description' => 'Keterangan singkat kejadian.',
        ];

        $response = $this->actingAs($user)->post(route('reports.store'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('crisis_reports', [
            'crisis_type_id' => $crisisType->id,
            'urgency_level_id' => $urgencyLevel->id,
            'region_id' => $region->id,
            'created_by' => $user->id,
            'status' => CrisisReport::STATUS_NEW,
            'address' => 'Jl. Uji Coba No. 1',
        ]);
    }

    public function test_validation_fails_without_required_fields(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan->value);

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'address' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors([
            'crisis_type_id',
            'urgency_level_id',
            'region_id',
            'occurred_at',
            'address',
            'latitude',
            'longitude',
            'description',
        ]);
    }

    public function test_user_without_create_permission_gets_403(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $crisisType = CrisisType::factory()->create();
        $urgencyLevel = UrgencyLevel::factory()->create();
        $region = Region::factory()->village()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'crisis_type_id' => $crisisType->id,
            'urgency_level_id' => $urgencyLevel->id,
            'region_id' => $region->id,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'address' => 'Jl. Larangan',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'description' => 'Coba akses tanpa izin.',
        ]);

        $response->assertForbidden();
    }
}
