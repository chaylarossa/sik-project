<?php

namespace App\Http\Controllers;

use App\Models\CrisisReport;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VerificationController extends Controller
{
    public function index(): View
    {
        $reports = CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region'])
            ->where('status', CrisisReport::STATUS_NEW)
            ->orderByDesc('occurred_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.verifications.index', [
            'reports' => $reports,
        ]);
    }

    public function approve(CrisisReport $report): RedirectResponse
    {
        $report->update(['status' => CrisisReport::STATUS_DONE]);

        return redirect()
            ->route('verifications.index')
            ->with('status', 'Laporan telah disetujui.');
    }

    public function reject(CrisisReport $report): RedirectResponse
    {
        $report->update(['status' => CrisisReport::STATUS_CLOSED]);

        return redirect()
            ->route('verifications.index')
            ->with('status', 'Laporan telah ditolak.');
    }
}
