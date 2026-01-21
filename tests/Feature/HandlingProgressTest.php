<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\HandlingUpdate;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandlingProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_update_succeeds_and_updates_report_status(): void
    {
        $this->seed(RbacSeeder::class);

        $operator = User::factory()->create();
        $operator->assignRole(RoleName::OperatorLapangan->value);

        $report = CrisisReport::factory()
            ->withVerificationStatus(CrisisReport::VERIFICATION_APPROVED)
            ->create();

        $payload = [
            'status' => CrisisReport::STATUS_IN_PROGRESS,
            'progress_percent' => 30,
            'note' => 'Mulai penanganan lapangan.',
            'occurred_at' => now()->subMinutes(15)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($operator)->post(route('reports.updates.store', $report), $payload);

        $response->assertRedirect(route('reports.timeline', $report));

        $this->assertDatabaseHas('handling_updates', [
            'report_id' => $report->id,
            'status' => CrisisReport::STATUS_IN_PROGRESS,
            'progress_percent' => 30,
            'updated_by' => $operator->id,
        ]);

        $this->assertDatabaseHas('crisis_reports', [
            'id' => $report->id,
            'status' => CrisisReport::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_backward_status_rejected_for_non_admin(): void
    {
        $this->seed(RbacSeeder::class);

        $operator = User::factory()->create();
        $operator->assignRole(RoleName::OperatorLapangan->value);

        $report = CrisisReport::factory()
            ->withStatus(CrisisReport::STATUS_DONE)
            ->create();

        HandlingUpdate::factory()->withStatus(CrisisReport::STATUS_DONE)->create([
            'report_id' => $report->id,
            'updated_by' => $operator->id,
            'occurred_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($operator)
            ->from(route('reports.timeline', $report))
            ->post(route('reports.updates.store', $report), [
                'status' => CrisisReport::STATUS_IN_PROGRESS,
                'progress_percent' => 50,
                'occurred_at' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('handling_updates', [
            'report_id' => $report->id,
            'status' => CrisisReport::STATUS_IN_PROGRESS,
        ]);

        $this->assertDatabaseHas('crisis_reports', [
            'id' => $report->id,
            'status' => CrisisReport::STATUS_DONE,
        ]);
    }

    public function test_user_without_permission_cannot_update_progress(): void
    {
        $this->seed(RbacSeeder::class);

        $publicUser = User::factory()->create();
        $publicUser->assignRole(RoleName::Publik->value);

        $report = CrisisReport::factory()->create();

        $response = $this->actingAs($publicUser)->post(route('reports.updates.store', $report), [
            'status' => CrisisReport::STATUS_IN_PROGRESS,
            'progress_percent' => 10,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertForbidden();
    }
}
