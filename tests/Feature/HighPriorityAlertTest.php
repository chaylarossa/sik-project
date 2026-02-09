<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use App\Notifications\CrisisHighPriorityNotification;
use App\Services\CrisisAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HighPriorityAlertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed Roles
        Role::firstOrCreate(['name' => RoleName::Administrator->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RoleName::Pimpinan->value, 'guard_name' => 'web']);
    }

    public function test_high_priority_report_triggers_notification_to_admin_and_pimpinan()
    {
        Notification::fake();

        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $pimpinan = User::factory()->create();
        $pimpinan->assignRole(RoleName::Pimpinan->value);

        $regularUser = User::factory()->create(); // Should not receive

        $highUrgency = UrgencyLevel::factory()->create(['is_high_priority' => true]);
        $report = CrisisReport::factory()->create([
            'urgency_level_id' => $highUrgency->id,
            'crisis_type_id' => CrisisType::factory()->create()->id,
            'region_id' => Region::factory()->create()->id,
        ]);
        $report->load('urgencyLevel'); // Ensure relationship is loaded

        // Act
        app(CrisisAlertService::class)->sendHighPriorityAlert($report);

        // Assert
        Notification::assertSentTo(
            [$admin, $pimpinan],
            CrisisHighPriorityNotification::class
        );

        Notification::assertNotSentTo(
            [$regularUser],
            CrisisHighPriorityNotification::class
        );
    }

    public function test_normal_priority_report_does_not_trigger_notification()
    {
        Notification::fake();

        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $normalUrgency = UrgencyLevel::factory()->create(['is_high_priority' => false]);
        $report = CrisisReport::factory()->create([
            'urgency_level_id' => $normalUrgency->id,
        ]);
        $report->load('urgencyLevel');

        // Act
        app(CrisisAlertService::class)->sendHighPriorityAlert($report);

        // Assert
        Notification::assertNothingSent();
    }

    public function test_throttling_prevents_duplicate_notifications_for_same_report()
    {
        Notification::fake();
        Cache::flush();

        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $highUrgency = UrgencyLevel::factory()->create(['is_high_priority' => true]);
        $report = CrisisReport::factory()->create([
            'urgency_level_id' => $highUrgency->id,
        ]);
        $report->load('urgencyLevel');

        // Act 1: First call
        app(CrisisAlertService::class)->sendHighPriorityAlert($report);

        // Assert 1: Sent
        Notification::assertSentToTimes($admin, CrisisHighPriorityNotification::class, 1);

        // Act 2: Second call (immediate)
        app(CrisisAlertService::class)->sendHighPriorityAlert($report);

        // Assert 2: Still 1 (throttled)
        Notification::assertSentToTimes($admin, CrisisHighPriorityNotification::class, 1);
    }

    public function test_store_endpoint_integration_triggers_alert()
    {
        Notification::fake();
        Cache::flush();

        // Arrange
        $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => PermissionName::CreateReport->value, 'guard_name' => 'web']);
        
        $reporter = User::factory()->create();
        $reporter->givePermissionTo(PermissionName::CreateReport->value); // Ensure permission

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Administrator->value);

        $highUrgency = UrgencyLevel::factory()->create(['is_high_priority' => true]);
        $crisisType = CrisisType::factory()->create();
        $region = Region::factory()->create(['level' => Region::LEVEL_VILLAGE]);

        $payload = [
            'crisis_type_id' => $crisisType->id,
            'urgency_level_id' => $highUrgency->id,
            'region_id' => $region->id,
            'description' => 'Emergency Test',
            'occurred_at' => now()->subHour()->toDateTimeString(),
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'address' => 'Jakarta',
        ];

        // Act
        $this->actingAs($reporter)
            ->post(route('reports.store'), $payload);

        // Assert
        Notification::assertSentTo(
            $admin,
            CrisisHighPriorityNotification::class
        );
    }
}
