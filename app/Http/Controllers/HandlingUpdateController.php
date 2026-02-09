<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHandlingUpdateRequest;
use App\Models\CrisisReport;
use App\Models\HandlingUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HandlingUpdateController extends Controller
{
    public function index(CrisisReport $report): View
    {
        $this->authorize('viewHandling', $report);

        $report->load(['crisisType', 'urgencyLevel', 'region.parent.parent.parent', 'creator']);

        $updates = $report->handlingUpdates()
            ->with('updatedBy')
            ->orderBy('occurred_at')
            ->orderBy('id')
            ->get();

        return view('crisis-reports.timeline', [
            'report' => $report,
            'updates' => $updates,
            'statusOptions' => CrisisReport::STATUSES,
        ]);
    }

    public function store(StoreHandlingUpdateRequest $request, CrisisReport $report): RedirectResponse
    {
        $this->authorize('updateProgress', $report);

        $data = $request->validated();
        $data['report_id'] = $report->id;
        $data['updated_by'] = $request->user()->id;

        DB::transaction(function () use ($report, $data): void {
            HandlingUpdate::create($data);
            $report->update(['status' => $data['status']]);
        });

        return redirect()
            ->route('reports.timeline', $report)
            ->with('status', 'Progres penanganan berhasil ditambahkan.');
    }
}
