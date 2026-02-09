<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTeamRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\UpdateProgressRequest;
use App\Models\CrisisHandling;
use App\Models\CrisisHandlingLog;
use App\Models\CrisisReport;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HandlingController extends Controller
{
    public function index(Request $request): View
    {
        $query = CrisisReport::with(['crisisType', 'urgencyLevel', 'region', 'handling', 'units'])
            ->where('verification_status', CrisisReport::VERIFICATION_APPROVED);

        // Filter: Status
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            // Jika filter 'BARU', bisa jadi handling belum dibuat ATAU memang statusnya BARU
            if ($status === CrisisHandling::STATUS_BARU) {
                $query->where(function ($q) {
                    $q->whereDoesntHave('handling')
                      ->orWhereHas('handling', fn($q2) => $q2->where('status', CrisisHandling::STATUS_BARU));
                });
            } else {
                $query->whereHas('handling', fn($q) => $q->where('status', $status));
            }
        }

        // Filter: Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', "%{$search}%")
                  ->orWhereHas('crisisType', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('region', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"));
            });
        }

        $reports = $query->latest()->paginate(10);

        return view('handling.index', compact('reports'));
    }

    public function show($id): View
    {
        $report = CrisisReport::with([
            'handling.logs.creator',
            'units',
            'crisisType',
            'urgencyLevel',
            'region',
            'creator'
        ])->findOrFail($id);

        $report->handling = $report->handling ?? new CrisisHandling(['status' => CrisisHandling::STATUS_BARU, 'progress' => 0]);

        // Eager load users for pivot display if possible, or just pass a cached list
        // For simplicity in this iteration, we just pass the report.
        
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $report->units->pluck('id'))
            ->get();
            
        return view('handling.show', compact('report', 'availableUnits'));
    }

    public function timeline($id): View
    {
        $report = CrisisReport::with(['handling.logs.creator'])->findOrFail($id);
        $logs = $report->handling ? $report->handling->logs : collect();

        return view('handling._timeline', ['logs' => $logs]);
    }

    public function assignTeam(AssignTeamRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        DB::transaction(function () use ($data) {
            $report = CrisisReport::findOrFail($data['crisis_report_id']);
            
            // Ensure handling record exists
            $handling = $report->handling()->firstOrCreate(
                [], 
                ['status' => CrisisHandling::STATUS_BARU, 'progress' => 0]
            );

            // Attach units
            $report->units()->attach($data['unit_ids'], [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'note' => $data['note'],
            ]);

            // Auto-update status from BARU to DALAM_PENANGANAN
            if ($handling->status === CrisisHandling::STATUS_BARU) {
                $handling->update([
                    'status' => CrisisHandling::STATUS_DALAM_PENANGANAN,
                    'started_at' => now(),
                ]);

                // Log Status Change implicitly
                $handling->logs()->create([
                    'type' => CrisisHandlingLog::TYPE_STATUS,
                    'payload' => [
                        'old_status' => CrisisHandling::STATUS_BARU,
                        'new_status' => CrisisHandling::STATUS_DALAM_PENANGANAN,
                        'note' => 'Otomatis berubah status saat penugasan tim awal.'
                    ],
                    'created_by' => auth()->id(),
                ]);
            }

            // Log Assignment
            $assignedUnitNames = Unit::whereIn('id', $data['unit_ids'])->pluck('name')->toArray();
            $handling->logs()->create([
                'type' => CrisisHandlingLog::TYPE_ASSIGNMENT,
                'payload' => [
                    'unit_ids' => $data['unit_ids'],
                    'unit_names' => $assignedUnitNames,
                    'note' => $data['note']
                ],
                'created_by' => auth()->id(),
            ]);

            // Notify Reporter (Creator)
            if ($report->creator) {
                $report->creator->notify(new \App\Notifications\AssignmentNotification($report, implode(', ', $assignedUnitNames)));
            }

            activity()
                ->performedOn($report)
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'units' => $assignedUnitNames,
                ])
                ->event('assigned')
                ->log('Menugaskan tim ke lokasi');
        });

        return back()->with('success', 'Tim berhasil ditugaskan.');
    }

    public function updateProgress(UpdateProgressRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $report = CrisisReport::findOrFail($data['crisis_report_id']);
            $handling = $report->handling; // Guaranteed to exist by request validation logic ideally, but create if fail-safe needed

            if (!$handling) {
                // Should be covered by validation or create here
                $handling = $report->handling()->create(['status' => CrisisHandling::STATUS_BARU]);
            }

            $oldProgress = $handling->progress;
            
            $handling->update([
                'progress' => $data['progress'],
                'current_note' => $data['description'],
            ]);

            $handling->logs()->create([
                'type' => CrisisHandlingLog::TYPE_PROGRESS,
                'payload' => [
                    'old_progress' => $oldProgress,
                    'new_progress' => $data['progress'],
                    'description' => $data['description']
                ],
                'created_by' => auth()->id(),
            ]);

            activity()
                ->performedOn($report)
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'progress' => $data['progress_percent'],
                ])
                ->event('progress_updated')
                ->log('Memperbarui progress penanganan');
        });

        return back()->with('success', 'Progress berhasil diperbarui.');
    }

    public function changeStatus(ChangeStatusRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $report = CrisisReport::findOrFail($data['crisis_report_id']);
            
            $handling = $report->handling;
            
            if (!$handling) {
                $handling = $report->handling()->create([
                    'status' => CrisisHandling::STATUS_BARU,
                    'progress' => 0
                ]);
            }

            $oldStatus = $handling->status;
            $newStatus = $data['status'];

            // Use setStatusSafely to handle dates and validation logic
            $handling->setStatusSafely($newStatus, auth()->user());
            $handling->save();

            $handling->logs()->create([
                'type' => CrisisHandlingLog::TYPE_STATUS,
                'payload' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'note' => $data['note']
                ],
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Status penanganan berhasil diubah.');
    }
}
