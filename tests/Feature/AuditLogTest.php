<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Models\CrisisReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Permission setup
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => PermissionName::ViewAuditLog->value, 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => PermissionName::CreateReport->value, 'guard_name' => 'web']);
    }

    public function test_create_report_generates_activity_log()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::CreateReport->value);

        $data = [
            'crisis_type_id' => \App\Models\CrisisType::factory()->create()->id,
            'urgency_level_id' => \App\Models\UrgencyLevel::factory()->create()->id,
            'region_id' => \App\Models\Region::factory()->create(['level' => \App\Models\Region::LEVEL_VILLAGE])->id,
            'description' => 'Test Log Report',
            'occurred_at' => now()->toDateTimeString(),
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Test Address',
        ];

        $this->actingAs($user)
            ->post(route('reports.store'), $data);

        $activity = Activity::latest()->first();

        $this->assertNotNull($activity);
        $this->assertEquals('created', $activity->event);
        $this->assertEquals('Membuat laporan krisis baru', $activity->description);
        $this->assertEquals($user->id, $activity->causer_id);
    }

    public function test_audit_log_viewer_authorization()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(PermissionName::ViewAuditLog->value);

        $unauthorized = User::factory()->create();

        // Authorized
        $this->actingAs($admin)
            ->get(route('audit-log.index'))
            ->assertStatus(200)
            ->assertViewIs('pages.audit.index');

        // Unauthorized
        $this->actingAs($unauthorized)
            ->get(route('audit-log.index'))
            ->assertStatus(403);
    }

    public function test_audit_log_filtering()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(PermissionName::ViewAuditLog->value);

        // Create logs
        activity()->causedBy($admin)->event('event_a')->log('Log A');
        activity()->causedBy($admin)->event('event_b')->log('Log B');

        $this->actingAs($admin)
            ->get(route('audit-log.index', ['event' => 'event_a']))
            ->assertSee('Log A')
            ->assertDontSee('Log B');
    }
}
