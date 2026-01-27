<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Models\CrisisReport;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\AssignmentNotification;
use App\Notifications\VerificationResultNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create necessary permissions
        \Spatie\Permission\Models\Permission::create(['name' => PermissionName::VerifyReport->value, 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::create(['name' => PermissionName::ManageHandling->value, 'guard_name' => 'web']);
    }

    public function test_reporter_receives_notification_on_approve()
    {
        Notification::fake();

        $user = User::factory()->create();
        $verifier = User::factory()->create();
        $verifier->givePermissionTo(PermissionName::VerifyReport);

        $report = CrisisReport::factory()->create([
            'created_by' => $user->id,
            'verification_status' => 'pending', // Explicit status
        ]);

        $this->actingAs($verifier)
            ->post(route('verifications.approve', $report));

        Notification::assertSentTo(
            [$user],
            VerificationResultNotification::class
        );
    }
    
    public function test_reporter_receives_notification_on_reject()
    {
        Notification::fake();

        $user = User::factory()->create();
        $verifier = User::factory()->create();
        $verifier->givePermissionTo(PermissionName::VerifyReport);

        $report = CrisisReport::factory()->create([
            'created_by' => $user->id,
            'verification_status' => 'pending',
        ]);

        $this->actingAs($verifier)
            ->post(route('verifications.reject', $report));

        Notification::assertSentTo(
            [$user],
            VerificationResultNotification::class
        );
    }

    public function test_assignee_receives_notification_on_assignment()
    {
        Notification::fake();

        $assigner = User::factory()->create();
        $assigner->givePermissionTo(PermissionName::ManageHandling);
        
        $assignee = User::factory()->create();
        // Ensure assignee is valid
        
        $report = CrisisReport::factory()->create([
            'verification_status' => CrisisReport::VERIFICATION_APPROVED,
        ]);
        
        $unit = Unit::factory()->create();

        $response = $this->actingAs($assigner)
            ->post(route('reports.assignments.store', $report), [
                'unit_id' => $unit->id,
                'assignee_id' => $assignee->id,
                'note' => 'Please handle asap',
                'status' => 'active',
            ]);

        Notification::assertSentTo(
            [$assignee],
            AssignmentNotification::class
        );
    }

    public function test_user_can_view_notifications()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertStatus(200)
            ->assertViewIs('notifications.index');
    }
}
