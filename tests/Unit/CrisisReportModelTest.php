<?php

namespace Tests\Unit;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class CrisisReportModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_status_is_set(): void
    {
        $report = CrisisReport::factory()->create();
        $report->refresh();

        $this->assertSame(CrisisReport::STATUS_NEW, $report->status);
    }

    public function test_filter_scope_applies_filters(): void
    {
        $typeA = CrisisType::factory()->create();
        $typeB = CrisisType::factory()->create();
        $urgencyA = UrgencyLevel::factory()->create();
        $urgencyB = UrgencyLevel::factory()->create();
        $regionA = Region::factory()->village()->create();
        $regionB = Region::factory()->village()->create();
        $user = User::factory()->create();

        $matching = CrisisReport::factory()->create([
            'crisis_type_id' => $typeA->id,
            'urgency_level_id' => $urgencyA->id,
            'region_id' => $regionA->id,
            'status' => CrisisReport::STATUS_NEW,
            'created_by' => $user->id,
        ]);

        CrisisReport::factory()->create([
            'crisis_type_id' => $typeB->id,
            'urgency_level_id' => $urgencyB->id,
            'region_id' => $regionB->id,
            'status' => CrisisReport::STATUS_CLOSED,
            'created_by' => $user->id,
        ]);

        $results = CrisisReport::query()
            ->filter([
                'crisis_type_id' => $typeA->id,
                'urgency_level_id' => $urgencyA->id,
                'region_id' => $regionA->id,
                'status' => CrisisReport::STATUS_NEW,
            ])
            ->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($matching));
    }

    public function test_occurred_at_is_cast_to_datetime(): void
    {
        $report = CrisisReport::factory()->create([
            'occurred_at' => '2026-01-10 08:30:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $report->occurred_at);
        $this->assertSame('2026-01-10 08:30:00', $report->occurred_at->format('Y-m-d H:i:s'));
    }
}
