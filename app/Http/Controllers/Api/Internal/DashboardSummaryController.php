<?php

namespace App\Http\Controllers\Api\Internal;

use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Internal\DashboardQueryRequest;
use App\Models\CrisisReport;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DashboardSummaryController extends Controller
{
    public function __invoke(DashboardQueryRequest $request)
    {
        $user = $request->user();
        if (! $user || ! $user->can(PermissionName::ViewDashboard->value)) {
            abort(403, 'Unauthorized');
        }

        $filters = $request->validated();
        $to = ! empty($filters['date_to'])
            ? CarbonImmutable::parse($filters['date_to'])->endOfDay()
            : CarbonImmutable::now()->endOfDay();
        $from = ! empty($filters['date_from'])
            ? CarbonImmutable::parse($filters['date_from'])->startOfDay()
            : $to->subDays(29)->startOfDay();

        $baseQuery = CrisisReport::query()->whereBetween('occurred_at', [$from, $to]);

        $statusCounts = $baseQuery->clone()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $cards = [
            'active' => ($statusCounts[CrisisReport::STATUS_NEW] ?? 0) + ($statusCounts[CrisisReport::STATUS_IN_PROGRESS] ?? 0),
            'pending_verification' => $statusCounts[CrisisReport::STATUS_NEW] ?? 0,
            'in_progress' => $statusCounts[CrisisReport::STATUS_IN_PROGRESS] ?? 0,
            'done_closed' => ($statusCounts[CrisisReport::STATUS_DONE] ?? 0) + ($statusCounts[CrisisReport::STATUS_CLOSED] ?? 0),
            'raw' => $statusCounts,
        ];

        $typeRows = $baseQuery->clone()
            ->join('crisis_types', 'crisis_reports.crisis_type_id', '=', 'crisis_types.id')
            ->select('crisis_types.name', DB::raw('COUNT(crisis_reports.id) as total'))
            ->groupBy('crisis_types.id', 'crisis_types.name')
            ->orderByDesc('total')
            ->get();

        $typeChart = [
            'labels' => $typeRows->pluck('name')->all(),
            'data' => $typeRows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];

        $trendRows = $baseQuery->clone()
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data = [];
        for ($date = $from; $date->lte($to); $date = $date->addDay()) {
            $labels[] = $date->format('d M');
            $data[] = (int) ($trendRows[$date->toDateString()] ?? 0);
        }

        return response()->json([
            'range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'stats' => $cards,
            'charts' => [
                'types' => $typeChart,
                'trend' => [
                    'labels' => $labels,
                    'data' => $data,
                ],
            ],
        ]);
    }
}
