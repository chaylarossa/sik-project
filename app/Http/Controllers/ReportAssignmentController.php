<?php

namespace App\Http\Controllers;

use App\Models\CrisisReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportAssignmentController extends Controller
{
    public function index(CrisisReport $report): RedirectResponse
    {
        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Penugasan laporan belum tersedia di versi ini.');
    }

    public function store(Request $request, CrisisReport $report): RedirectResponse
    {
        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Penyimpanan penugasan belum tersedia di versi ini.');
    }
}
