<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\CrisisHandling;
use App\Models\CrisisReport;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\UnitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrisisHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->seed(UnitSeeder::class);
    }

    public function test_assign_team_changes_status_to_in_progress()
    {
        // 1. User Login (Operator/Verifier/Admin)
        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan);
        
        // 2. Create Crisis Report
        $report = CrisisReport::factory()->create([
            'status' => 'new' // Assuming 'new' is initial status constant
        ]);
        
        $unit = Unit::first();

        // 3. Assign Team
        $response = $this->actingAs($user)
            ->post(route('handling.assign'), [
                'crisis_report_id' => $report->id,
                'unit_ids' => [$unit->id],
                'note' => 'Segera meluncur',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify Handling Created & Status Updated
        $this->assertDatabaseHas('crisis_handlings', [
            'crisis_report_id' => $report->id,
            'status' => CrisisHandling::STATUS_DALAM_PENANGANAN,
        ]);

        $this->assertDatabaseHas('crisis_report_unit', [
            'crisis_report_id' => $report->id,
            'unit_id' => $unit->id,
            'assigned_by' => $user->id,
        ]);

        $this->assertDatabaseHas('crisis_handling_logs', [
            'type' => 'ASSIGNMENT',
        ]);
    }

    public function test_update_progress()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan);

        $report = CrisisReport::factory()->create();
        // Initialize handling
        $handling = $report->handling()->create([
            'status' => CrisisHandling::STATUS_DALAM_PENANGANAN,
            'progress' => 10
        ]);

        $response = $this->actingAs($user)
            ->post(route('handling.progress'), [
                'crisis_report_id' => $report->id,
                'progress' => 50,
                'description' => 'Evakuasi sedang berlangsung',
            ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('crisis_handlings', [
            'id' => $handling->id,
            'progress' => 50,
            'current_note' => 'Evakuasi sedang berlangsung',
        ]);
        
        $this->assertDatabaseHas('crisis_handling_logs', [
            'type' => 'PROGRESS',
            'created_by' => $user->id
        ]);
    }

    public function test_set_status_finished_requires_100_progress()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::OperatorLapangan);

        $report = CrisisReport::factory()->create();
        $report->handling()->create([
            'status' => CrisisHandling::STATUS_DALAM_PENANGANAN,
            'progress' => 90 // Not 100
        ]);

        // Attempt to set SELESAI
        $response = $this->actingAs($user)
            ->post(route('handling.status'), [
                'crisis_report_id' => $report->id,
                'status' => CrisisHandling::STATUS_SELESAI,
                'note' => 'Selesai',
            ]);

        $response->assertSessionHasErrors(['status']); // Should fail

        // Update progress to 100 first
        $report->handling->update(['progress' => 100]);

        // Attempt to set SELESAI again
        $response = $this->actingAs($user)
            ->post(route('handling.status'), [
                'crisis_report_id' => $report->id,
                'status' => CrisisHandling::STATUS_SELESAI,
                'note' => 'Selesai',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('crisis_handlings', [
            'id' => $report->handling->id,
            'status' => CrisisHandling::STATUS_SELESAI,
        ]);
        
        $report->refresh();
        $this->assertNotNull($report->handling->finished_at);
    }

    public function test_only_admin_can_close_report()
    {
        // 1. Ordinary User (Operator) -> Cannot Close
        $operator = User::factory()->create();
        $operator->assignRole(RoleName::OperatorLapangan);

        $report = CrisisReport::factory()->create();
        $report->handling()->create([
            'status' => CrisisHandling::STATUS_SELESAI, 
            'progress' => 100
        ]);

        $response = $this->actingAs($operator)
            ->post(route('handling.status'), [
                'crisis_report_id' => $report->id,
                'status' => CrisisHandling::STATUS_DITUTUP,
                'note' => 'Closing',
            ]);
        
        $response->assertSessionHasErrors(['status']); // Authorization/Validation error

        // 2. Admin -> Can Close
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator);

        $response = $this->actingAs($admin)
            ->post(route('handling.status'), [
                'crisis_report_id' => $report->id,
                'status' => CrisisHandling::STATUS_DITUTUP,
                'note' => 'Closing officially',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('crisis_handlings', [
            'id' => $report->handling->id,
            'status' => CrisisHandling::STATUS_DITUTUP,
            'closed_by' => $admin->id,
        ]);
        
        $report->refresh();
        $this->assertNotNull($report->handling->closed_at);
        $this->assertTrue($report->handling->isClosed());
    }
}
