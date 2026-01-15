<?php

namespace Tests\Unit;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CrisisReportModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_status_is_set(): void
    {
        $report = CrisisReport::factory()->create();
        $report->refresh();

        $this->assertSame('pending', $report->verification_status);
        $this->assertSame('new', $report->handling_status);
    }

    public function test_filter_scope_applies_filters(): void
    {
        $typeA = CrisisType::factory()->create();
        $typeB = CrisisType::factory()->create();
        $urgencyA = UrgencyLevel::factory()->create();
        $urgencyB = UrgencyLevel::factory()->create();
        $user = User::factory()->create();

        $matching = CrisisReport::factory()->create([
            'crisis_type_id' => $typeA->id,
            'urgency_level_id' => $urgencyA->id,
            'region_id' => 101,
            'handling_status' => 'new',
            'verification_status' => 'pending',
            'created_by' => $user->id,
        ]);

        CrisisReport::factory()->create([
            'crisis_type_id' => $typeB->id,
            'urgency_level_id' => $urgencyB->id,
            'region_id' => 202,
            'handling_status' => 'closed',
            'verification_status' => 'verified',
            'created_by' => $user->id,
        ]);

        $results = CrisisReport::query()
            ->filter([
                'crisis_type_id' => $typeA->id,
                'urgency_level_id' => $urgencyA->id,
                'region_id' => 101,
                'handling_status' => 'new',
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
