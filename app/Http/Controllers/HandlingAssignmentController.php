<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Http\Requests\StoreHandlingAssignmentRequest;
use App\Models\CrisisReport;
use App\Models\HandlingAssignment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HandlingAssignmentController extends Controller
{
    public function index(CrisisReport $report): View
    {
        $this->authorize('viewHandling', $report);

        $report->load(['crisisType', 'urgencyLevel', 'region.parent.parent.parent', 'creator']);

        $assignments = HandlingAssignment::query()
            ->with(['unit', 'assignee', 'assignedBy'])
            ->where('report_id', $report->id)
            ->latest()
            ->get();

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $assignees = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', '!=', RoleName::Publik->value))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('crisis-reports.assignments', [
            'report' => $report,
            'assignments' => $assignments,
            'units' => $units,
            'assignees' => $assignees,
            'statusOptions' => HandlingAssignment::STATUSES,
        ]);
    }

    public function store(StoreHandlingAssignmentRequest $request, CrisisReport $report): RedirectResponse
    {
        $this->authorize('assign', $report);

        $data = $request->validated();
        $data['report_id'] = $report->id;
        $data['assigned_by'] = $request->user()->id;
        $data['status'] = $data['status'] ?? HandlingAssignment::STATUS_ACTIVE;

        DB::transaction(function () use ($report, $data): void {
            $hasActiveAssignment = HandlingAssignment::query()
                ->where('report_id', $report->id)
                ->where('status', HandlingAssignment::STATUS_ACTIVE)
                ->exists();

            HandlingAssignment::create($data);

            if (! $hasActiveAssignment
                && $data['status'] === HandlingAssignment::STATUS_ACTIVE
                && $report->status === CrisisReport::STATUS_NEW) {
                $report->update(['status' => CrisisReport::STATUS_IN_PROGRESS]);
            }
        });

        return redirect()
            ->route('reports.assignments.index', $report)
            ->with('status', 'Penugasan berhasil ditambahkan.');
    }
}
