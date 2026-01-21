<?php

namespace App\Http\Controllers;

use App\Models\CrisisReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportTimelineController extends Controller
{
    public function show(CrisisReport $report): RedirectResponse
    {
        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Timeline laporan belum tersedia di versi ini.');
    }

    public function store(Request $request, CrisisReport $report): RedirectResponse
    {
        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Pembaruan timeline belum tersedia di versi ini.');
    }
}
