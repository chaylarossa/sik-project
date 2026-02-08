<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Notifications\VerificationResultNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('verify', CrisisReport::class);

        $filterStatus = $request->string('status')->toString();
        $status = in_array($filterStatus, CrisisReport::VERIFICATION_STATUSES, true)
            ? $filterStatus
            : CrisisReport::VERIFICATION_PENDING;

        $reports = CrisisReport::query()
            ->with(['crisisType', 'urgencyLevel', 'region', 'creator'])
            ->where('verification_status', $status)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $counters = CrisisReport::query()
            ->selectRaw('verification_status, count(*) as total')
            ->groupBy('verification_status')
            ->pluck('total', 'verification_status');

        return view('pages.verifications.index', [
            'reports' => $reports,
            'status' => $status,
            'counters' => $counters,
            'statusOptions' => CrisisReport::VERIFICATION_STATUSES,
        ]);
    }

    public function approve(CrisisReport $report): RedirectResponse
    {
        $this->authorize('verify', $report);

        $report->update(['verification_status' => CrisisReport::VERIFICATION_APPROVED]);

        $report->creator->notify(new VerificationResultNotification($report));

        activity()
            ->performedOn($report)
            ->causedBy(auth()->user()) // Helper auth() or request()->user()
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event('verified')
            ->log('Menyetujui laporan krisis');

        return back()->with('status', 'Laporan berhasil disetujui.');
    }

    public function reject(CrisisReport $report): RedirectResponse
    {
        $this->authorize('verify', $report);

        $report->update(['verification_status' => CrisisReport::VERIFICATION_REJECTED]);

        $report->creator->notify(new VerificationResultNotification($report));

        activity()
            ->performedOn($report)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event('verified')
            ->log('Menolak laporan krisis');

        return back()->with('status', 'Laporan ditolak.');
    }
}
