<?php

namespace App\Services;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function cards(): array
    {
        $base = array_fill_keys(CrisisReport::STATUSES, 0);

        $counts = CrisisReport::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = array_merge($base, $counts);

        return [
            'active' => ($statuses[CrisisReport::STATUS_NEW] ?? 0) + ($statuses[CrisisReport::STATUS_IN_PROGRESS] ?? 0),
            'pending_verification' => $statuses[CrisisReport::STATUS_NEW] ?? 0,
            'in_progress' => $statuses[CrisisReport::STATUS_IN_PROGRESS] ?? 0,
            'done_closed' => ($statuses[CrisisReport::STATUS_DONE] ?? 0) + ($statuses[CrisisReport::STATUS_CLOSED] ?? 0),
            'raw' => $statuses,
        ];
    }

    public function crisisTypeChart(): array
    {
        $rows = CrisisReport::query()
            ->join('crisis_types', 'crisis_reports.crisis_type_id', '=', 'crisis_types.id')
            ->select('crisis_types.name', DB::raw('COUNT(crisis_reports.id) as total'))
            ->groupBy('crisis_types.id', 'crisis_types.name')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('name')->all();
        $data = $rows->pluck('total')->map(fn ($v) => (int) $v)->all();

        $palette = ['#4338CA', '#6366F1', '#A5B4FC', '#22C55E', '#F59E0B', '#EF4444'];
        $colors = collect($labels)
            ->map(fn ($_, $i) => $palette[$i % count($palette)])
            ->all();

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    public function dailyTrend(int $days = 7): array
    {
        $end = CarbonImmutable::now()->endOfDay();
        $start = $end->subDays($days - 1)->startOfDay();

        $rows = CrisisReport::query()
            ->whereBetween('occurred_at', [$start, $end])
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data = [];

        for ($date = $start; $date->lte($end); $date = $date->addDay()) {
            $labels[] = $date->format('d M');
            $data[] = (int) ($rows[$date->toDateString()] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function recentReports(int $limit = 6)
    {
        return CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region'])
            ->latest('occurred_at')
            ->limit($limit)
            ->get();
    }

    public function pendingVerifications(int $limit = 10)
    {
        return CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region', 'creator'])
            ->where('status', CrisisReport::STATUS_NEW)
            ->latest('occurred_at')
            ->limit($limit)
            ->get();
    }

    public function operatorQueue(int $limit = 10)
    {
        return CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region'])
            ->whereIn('status', [CrisisReport::STATUS_IN_PROGRESS, CrisisReport::STATUS_DONE, CrisisReport::STATUS_CLOSED])
            ->latest('occurred_at')
            ->limit($limit)
            ->get();
    }
}
