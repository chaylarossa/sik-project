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
        
        $reporter = User::factory()->create();

        $report = CrisisReport::factory()->create([
            'verification_status' => CrisisReport::VERIFICATION_APPROVED,
            'created_by' => $reporter->id,
        ]);
        
        $unit = Unit::factory()->create();

        $response = $this->actingAs($assigner)
            ->post(route('handling.assign'), [
                'crisis_report_id' => $report->id,
                'unit_ids' => [$unit->id],
                'note' => 'Please handle asap',
            ]);

        Notification::assertSentTo(
            [$reporter],
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

    public function test_admins_receive_notification_on_high_priority_report()
    {
        Notification::fake();

        // Setup Permissions/Roles
        $roleAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => \App\Enums\RoleName::Administrator->value, 'guard_name' => 'web']);
        $permCreate = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => \App\Enums\PermissionName::CreateReport->value, 'guard_name' => 'web']);
        
        $admin = User::factory()->create();
        $admin->assignRole(\App\Enums\RoleName::Administrator->value);

        $reporter = User::factory()->create();
        $reporter->givePermissionTo(\App\Enums\PermissionName::CreateReport->value);

        $urgency = \App\Models\UrgencyLevel::factory()->create(['is_high_priority' => true]);
        $crisisType = \App\Models\CrisisType::factory()->create(['is_active' => true]);
        
        // Region Level Village
        $region = \App\Models\Region::factory()->create(['level' => \App\Models\Region::LEVEL_VILLAGE]); 

        $data = [
           'crisis_type_id' => $crisisType->id,
           'urgency_level_id' => $urgency->id,
           'region_id' => $region->id,
           'description' => 'Huge Fire!',
           'occurred_at' => now()->toDateTimeString(),
           'latitude' => -6.2,
           'longitude' => 106.8,
           'address' => 'Test Address',
        ];

        $this->actingAs($reporter)
            ->post(route('reports.store'), $data)
            ->assertRedirect();

        Notification::assertSentTo(
            [$admin],
            \App\Notifications\CrisisHighPriorityNotification::class
        );
    }
}
