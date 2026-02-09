<?php

namespace App\Http\Controllers\Api\Internal;

use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Internal\MapFilterRequest;
use App\Models\CrisisHandling;
use App\Models\CrisisReport;
use App\Models\Region;
use Carbon\CarbonImmutable;

class MapCrisisPointController extends Controller
{
    public function __invoke(MapFilterRequest $request)
    {
        $user = $request->user();
        if (! $user || ! $user->can(PermissionName::ViewMaps->value)) {
            abort(403, 'Unauthorized');
        }

        $filters = $request->validated();
        $limit = min($filters['limit'] ?? 500, 500);

        $query = CrisisReport::query()
            ->with(['crisisType:id,name', 'urgencyLevel:id,name', 'region:id,name,parent_id', 'handling'])
            ->when($filters['crisis_type_id'] ?? null, fn ($q, $value) => $q->where('crisis_type_id', $value));

        if (! empty($filters['region_id'])) {
            $region = Region::query()->find($filters['region_id']);

            if ($region) {
                match ($region->level) {
                    Region::LEVEL_VILLAGE => $query->where('region_id', $region->id),
                    Region::LEVEL_DISTRICT => $query->whereHas('region.parent', fn ($q) => $q->where('id', $region->id)),
                    Region::LEVEL_CITY => $query->whereHas('region.parent.parent', fn ($q) => $q->where('id', $region->id)),
                    Region::LEVEL_PROVINCE => $query->whereHas('region.parent.parent.parent', fn ($q) => $q->where('id', $region->id)),
                    default => null,
                };
            }
        }

        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $from = ! empty($filters['date_from'])
                ? CarbonImmutable::parse($filters['date_from'])->startOfDay()
                : CarbonImmutable::now()->subDays(30)->startOfDay();
            $to = ! empty($filters['date_to'])
                ? CarbonImmutable::parse($filters['date_to'])->endOfDay()
                : CarbonImmutable::now()->endOfDay();

            $query->whereBetween('occurred_at', [$from, $to]);
        }

        if (! empty($filters['handling_status'])) {
            $status = $filters['handling_status'];

            if ($status === CrisisHandling::STATUS_BARU) {
                $query->where(function ($q) {
                    $q->whereDoesntHave('handling')
                        ->orWhereHas('handling', fn ($q2) => $q2->where('status', CrisisHandling::STATUS_BARU));
                });
            } else {
                $query->whereHas('handling', fn ($q) => $q->where('status', $status));
            }
        }

        $points = $query
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get()
            ->map(function (CrisisReport $report) {
                $status = $report->handling?->status ?? CrisisHandling::STATUS_BARU;

                return [
                    'id' => $report->id,
                    'lat' => (float) $report->latitude,
                    'lng' => (float) $report->longitude,
                    'type' => $report->crisisType?->name ?? '-',
                    'urgency' => $report->urgencyLevel?->name ?? '-',
                    'status' => $status,
                    'occurred_at' => optional($report->occurred_at)->format('d M Y, H:i'),
                ];
            })
            ->values();

        return response()->json([
            'data' => $points,
            'meta' => [
                'count' => $points->count(),
                'limit' => $limit,
            ],
        ]);
    }
}
