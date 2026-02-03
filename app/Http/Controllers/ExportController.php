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
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
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

        $query->latest();

        // Get all records, no pagination for export
        $reports = $query->get();

        $data = [
            'reports' => $reports,
            'filters' => $validated,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.archive-pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('archive-reports-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    public function archive(Request $request)
    {
        $filters = [
            'crisis_type_id' => $request->input('crisis_type_id'),
            'verification_status' => $request->input('verification_status'),
            'handling_status' => $request->input('handling_status'),
            'region_id' => $request->input('region_id'),
            'period' => [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ];

        $reports = CrisisReport::with(['crisisType', 'urgencyLevel'])
            ->filter($filters)
            ->orderBy('occurred_at', 'desc')
            ->get();

        return Excel::download(new CrisisArchiveExport($reports), 'arsip-laporan.xlsx');
    }
}
