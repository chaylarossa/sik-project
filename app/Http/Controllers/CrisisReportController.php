<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCrisisReportRequest;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrisisReportController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CrisisReport::class, 'report');
    }

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $crisisTypeId = $request->integer('crisis_type_id') ?: null;
        $provinceId = $request->integer('region_id') ?: null;
        $periodStart = $request->filled('period_start') ? Carbon::parse($request->input('period_start')) : null;
        $periodEnd = $request->filled('period_end') ? Carbon::parse($request->input('period_end')) : null;

        $regionFilter = $provinceId
            ? Region::query()
                ->where('level', Region::LEVEL_VILLAGE)
                ->whereHas('parent.parent.parent', fn ($q) => $q->where('id', $provinceId))
                ->pluck('id')
            : collect();

        $reports = CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region', 'creator'])
            ->when($crisisTypeId, fn ($query) => $query->where('crisis_type_id', $crisisTypeId))
            ->when($provinceId, fn ($query) => $query->whereIn('region_id', $regionFilter))
            ->when(in_array($status, CrisisReport::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($periodStart, fn ($query) => $query->where('occurred_at', '>=', $periodStart->copy()->startOfDay()))
            ->when($periodEnd, fn ($query) => $query->where('occurred_at', '<=', $periodEnd->copy()->endOfDay()))
            ->orderByDesc('occurred_at')
            ->paginate(10)
            ->withQueryString();

        return view('crisis-reports.index', [
            'reports' => $reports,
            'filters' => [
                'crisis_type_id' => $crisisTypeId,
                'region_id' => $provinceId,
                'status' => in_array($status, CrisisReport::STATUSES, true) ? $status : null,
                'period_start' => $periodStart?->toDateString(),
                'period_end' => $periodEnd?->toDateString(),
            ],
            'crisisTypes' => CrisisType::query()->orderBy('name')->get(['id', 'name']),
            'urgencyLevels' => UrgencyLevel::query()->orderBy('level')->get(['id', 'name', 'color', 'level']),
            'regions' => Region::query()
                ->where('level', Region::LEVEL_PROVINCE)
                ->select('name', DB::raw('MIN(id) as id'))
                ->groupBy('name')
                ->orderBy('name')
                ->get()
                ->map(fn ($item) => (object) ['id' => (int) $item->id, 'name' => $item->name]),
            'statusOptions' => CrisisReport::STATUSES,
        ]);
    }

    public function create(): View
    {
        $javaProvinceCodes = ['BNT', 'DKI', 'JBR', 'JTG', 'DIY', 'JTM'];

        $provinces = Region::query()
            ->where('level', Region::LEVEL_PROVINCE)
            ->whereIn('code', $javaProvinceCodes)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $cities = Region::query()
            ->where('level', Region::LEVEL_CITY)
            ->whereIn('parent_id', $provinces->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        $districts = Region::query()
            ->where('level', Region::LEVEL_DISTRICT)
            ->whereIn('parent_id', $cities->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        $villages = Region::query()
            ->where('level', Region::LEVEL_VILLAGE)
            ->whereIn('parent_id', $districts->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('crisis-reports.create', [
            'crisisTypes' => CrisisType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'urgencyLevels' => UrgencyLevel::query()->orderBy('level')->get(['id', 'name', 'level']),
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }

    public function store(StoreCrisisReportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['status'] = CrisisReport::STATUS_NEW;

        $report = CrisisReport::create($data);

        // Trigger High Priority Alert via Service
        $report->load(['urgencyLevel', 'crisisType', 'region']);
        app(\App\Services\CrisisAlertService::class)->sendHighPriorityAlert($report);

        activity()
            ->performedOn($report)
            ->causedBy($request->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->event('created')
            ->log('Membuat laporan krisis baru');

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Laporan krisis berhasil dibuat.');
    }

    public function show(CrisisReport $report): View
    {
        $report->load([
            'crisisType',
            'urgencyLevel',
            'region.parent.parent.parent',
            'creator',
            'verifications.verifier',
            'media',
            'handlingUpdates' => function ($query) {
                $query->with('updatedBy')
                    ->orderByDesc('occurred_at')
                    ->orderByDesc('id')
                    ->limit(5);
            },
        ]);

        return view('crisis-reports.show', [
            'report' => $report,
        ]);
    }
}
