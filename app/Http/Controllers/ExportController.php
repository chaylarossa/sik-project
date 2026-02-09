<?php

namespace App\Http\Controllers;

use App\Exports\CrisisArchiveExport;
use App\Http\Requests\ArchiveFilterRequest;
use App\Models\CrisisReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportPdf(ArchiveFilterRequest $request)
    {
        // Reuse validation from ArchiveFilterRequest
        $validated = $request->validated();

        $query = CrisisReport::with([
            'crisisType', 
            'urgencyLevel', 
            'region', 
            'creator'
        ]);

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        if ($request->filled('crisis_type_id')) {
            $query->where('crisis_type_id', $request->crisis_type_id);
        }

        if ($request->filled('urgency_level_id')) {
            $query->where('urgency_level_id', $request->urgency_level_id);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->q . '%')
                  ->orWhere('address', 'like', '%' . $request->q . '%');
            });
        }

        $query->latest('occurred_at');

        // Get all records, no pagination for export
        $reports = $query->get();

        $data = [
            'reports' => $reports,
            'filters' => $validated,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.archive-pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'filters' => $validated,
            ])
            ->event('exported')
            ->log('Mengunduh laporan krisis (PDF)');

        return $pdf->download('archive-reports-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    public function archive(ArchiveFilterRequest $request)
    {
        $validated = $request->validated();

        $query = CrisisReport::with([
            'crisisType', 
            'urgencyLevel', 
            'region', 
            'creator'
        ]);

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        if ($request->filled('crisis_type_id')) {
            $query->where('crisis_type_id', $request->crisis_type_id);
        }

        if ($request->filled('urgency_level_id')) {
            $query->where('urgency_level_id', $request->urgency_level_id);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->q . '%')
                  ->orWhere('address', 'like', '%' . $request->q . '%');
            });
        }

        $query->latest('occurred_at');

        $reports = $query->get();

        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'filters' => $validated,
            ])
            ->event('exported')
            ->log('Mengunduh arsip laporan (Excel)');

        return Excel::download(new CrisisArchiveExport($reports), 'arsip-laporan.xlsx');
    }
}
