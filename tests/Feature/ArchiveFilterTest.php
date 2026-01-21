<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ArchiveFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure permission exists
        Permission::firstOrCreate(['name' => PermissionName::ExportData->value]);
    }

    public function test_can_view_archive_page()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $response = $this->actingAs($user)->get(route('archive.index'));

        $response->assertStatus(200);
        $response->assertViewIs('archive.index');
    }

    public function test_cannot_view_archive_without_permission()
    {
        $user = User::factory()->create();
        // User does not have permission

        $response = $this->actingAs($user)->get(route('archive.index'));

        $response->assertStatus(403);
    }

    public function test_can_filter_archive_by_date()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $dateBefore = now()->subDays(10);
        $dateTarget = now()->subDays(5);
        $dateAfter = now()->subDays(1);

        CrisisReport::factory()->create(['created_at' => $dateBefore]);
        $targetReport = CrisisReport::factory()->create(['created_at' => $dateTarget]);
        CrisisReport::factory()->create(['created_at' => $dateAfter]);

        $response = $this->actingAs($user)->get(route('archive.index', [
            'date_from' => $dateTarget->format('Y-m-d'),
            'date_to' => $dateTarget->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reports', function ($reports) use ($targetReport) {
            return $reports->count() === 1 && $reports->first()->id === $targetReport->id;
        });
    }

    public function test_filter_by_attributes()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $type1 = CrisisType::factory()->create();
        $type2 = CrisisType::factory()->create();

        $report1 = CrisisReport::factory()->create(['crisis_type_id' => $type1->id, 'status' => 'new']);
        $report2 = CrisisReport::factory()->create(['crisis_type_id' => $type2->id, 'status' => 'done']);

        $response = $this->actingAs($user)->get(route('archive.index', [
            'crisis_type_id' => $type1->id,
            'status' => 'new',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reports', function ($reports) use ($report1) {
            return $reports->count() === 1 && $reports->first()->id === $report1->id;
        });
    }

    public function test_filter_by_date_to_only()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $dateTarget = now()->subDays(5);
        $dateAfter = now()->subDays(1);

        CrisisReport::factory()->create(['created_at' => $dateTarget]);
        CrisisReport::factory()->create(['created_at' => $dateAfter]);

        $response = $this->actingAs($user)->get(route('archive.index', [
            'date_to' => $dateTarget->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reports', function ($reports) {
            return $reports->count() === 1;
        });
    }

    public function test_validation_error_on_invalid_date()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $response = $this->actingAs($user)->get(route('archive.index', [
            'date_from' => 'invalid-date',
        ]));

        $response->assertSessionHasErrors('date_from');
    }

    public function test_validation_error_date_range()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        $response = $this->actingAs($user)->get(route('archive.index', [
            'date_from' => '2023-01-02',
            'date_to' => '2023-01-01', // Date to is before date from
        ]));

        $response->assertSessionHasErrors('date_to');
    }
}
