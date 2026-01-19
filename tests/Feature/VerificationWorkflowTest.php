<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\User;
use App\Models\Verification;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_verifikator_can_approve_report(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $report = CrisisReport::factory()->create([
            'verification_status' => CrisisReport::VERIFICATION_PENDING,
        ]);

        $response = $this->actingAs($user)->post(route('reports.verify.store', $report), [
            'action' => 'approve',
        ]);

        $response->assertRedirect(route('reports.show', $report));

        $this->assertDatabaseHas('crisis_reports', [
            'id' => $report->id,
            'verification_status' => Verification::STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('verifications', [
            'crisis_report_id' => $report->id,
            'verified_by' => $user->id,
            'status' => Verification::STATUS_APPROVED,
        ]);
    }

    public function test_operator_cannot_verify_report(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan->value);

        $report = CrisisReport::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.verify.store', $report), [
            'action' => 'approve',
        ]);

        $response->assertForbidden();
    }

    public function test_reject_requires_note(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $report = CrisisReport::factory()->create([
            'verification_status' => CrisisReport::VERIFICATION_PENDING,
        ]);

        $response = $this->actingAs($user)->post(route('reports.verify.store', $report), [
            'action' => 'reject',
        ]);

        $response->assertSessionHasErrors(['note']);

        $this->assertDatabaseHas('crisis_reports', [
            'id' => $report->id,
            'verification_status' => CrisisReport::VERIFICATION_PENDING,
        ]);

        $this->assertDatabaseMissing('verifications', [
            'crisis_report_id' => $report->id,
        ]);
    }
}
