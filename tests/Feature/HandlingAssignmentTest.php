<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\HandlingAssignment;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandlingAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_success_when_report_approved(): void
    {
        $this->seed(RbacSeeder::class);

        $assigner = User::factory()->create();
        $assigner->assignRole(RoleName::OperatorLapangan->value);

        $assignee = User::factory()->create();
        $assignee->assignRole(RoleName::OperatorLapangan->value);

        $unit = Unit::factory()->create();

        $report = CrisisReport::factory()
            ->withVerificationStatus(CrisisReport::VERIFICATION_APPROVED)
            ->create();

        $response = $this->actingAs($assigner)->post(route('reports.assignments.store', $report), [
            'unit_id' => $unit->id,
            'assignee_id' => $assignee->id,
            'status' => HandlingAssignment::STATUS_ACTIVE,
            'note' => 'Unit siap menangani.',
        ]);

        $response->assertRedirect(route('reports.assignments.index', $report));

        $this->assertDatabaseHas('handling_assignments', [
            'report_id' => $report->id,
            'unit_id' => $unit->id,
            'assignee_id' => $assignee->id,
            'assigned_by' => $assigner->id,
            'status' => HandlingAssignment::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('crisis_reports', [
            'id' => $report->id,
            'status' => CrisisReport::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_assign_fails_when_report_not_approved(): void
    {
        $this->seed(RbacSeeder::class);

        $assigner = User::factory()->create();
        $assigner->assignRole(RoleName::OperatorLapangan->value);

        $assignee = User::factory()->create();
        $assignee->assignRole(RoleName::OperatorLapangan->value);

        $unit = Unit::factory()->create();

        $report = CrisisReport::factory()
            ->withVerificationStatus(CrisisReport::VERIFICATION_PENDING)
            ->create();

        $response = $this->actingAs($assigner)
            ->from(route('reports.assignments.index', $report))
            ->post(route('reports.assignments.store', $report), [
                'unit_id' => $unit->id,
                'assignee_id' => $assignee->id,
                'status' => HandlingAssignment::STATUS_ACTIVE,
            ]);

        $response->assertSessionHasErrors('report_id');
        $this->assertDatabaseMissing('handling_assignments', [
            'report_id' => $report->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_user_without_permission_cannot_assign(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Publik->value);

        $assignee = User::factory()->create();
        $assignee->assignRole(RoleName::OperatorLapangan->value);

        $unit = Unit::factory()->create();

        $report = CrisisReport::factory()
            ->withVerificationStatus(CrisisReport::VERIFICATION_APPROVED)
            ->create();

        $response = $this->actingAs($user)->post(route('reports.assignments.store', $report), [
            'unit_id' => $unit->id,
            'assignee_id' => $assignee->id,
            'status' => HandlingAssignment::STATUS_ACTIVE,
        ]);

        $response->assertForbidden();
    }
}
