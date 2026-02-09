<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyCrisisReportRequest;
use App\Models\CrisisReport;
use App\Models\Verification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Notifications\VerificationResultNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
<<<<<<< HEAD
    public function index(): View
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

            ]);

            $lockedReport->update([
                'verification_status' => $status,
            ]);

            return redirect()
                ->route('reports.show', $lockedReport)
                ->with('status', $status === Verification::STATUS_APPROVED
                    ? 'Laporan berhasil disetujui.'
                    : 'Laporan berhasil ditolak.');
        });

        public function create(CrisisReport $report): View
        {
            $this->authorize('verify', $report);

            if ($report->verification_status !== CrisisReport::VERIFICATION_PENDING) {
                abort(403, 'Laporan sudah diverifikasi sebelumnya.');
            }

            $report->load(['crisisType', 'urgencyLevel', 'region', 'creator', 'latestVerification.verifier']);

            return view('crisis-reports.verify', [
                'report' => $report,
            ]);
        }

        public function store(VerifyCrisisReportRequest $request, CrisisReport $report): RedirectResponse
        {
            $data = $request->validated();
            $user = $request->user();

            return DB::transaction(function () use ($report, $data, $user) {
                $lockedReport = CrisisReport::query()
                    ->whereKey($report->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedReport->verification_status !== CrisisReport::VERIFICATION_PENDING) {
                    return redirect()
                        ->route('reports.show', $lockedReport)
                        ->with('error', 'Laporan sudah diverifikasi sebelumnya.');
                }

                $status = $data['action'] === 'approve'
                    ? Verification::STATUS_APPROVED
                    : Verification::STATUS_REJECTED;

                Verification::create([
                    'crisis_report_id' => $lockedReport->id,
                    'verified_by' => $user->id,
                    'status' => $status,
                    'note' => $data['note'] ?? null,
                ]);

                $lockedReport->update([
                    'verification_status' => $status,
                ]);

                $lockedReport->creator->notify(new VerificationResultNotification($lockedReport));

                activity()
                    ->performedOn($lockedReport)
                    ->causedBy($user)
                    ->withProperties([
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ])
                    ->event('verified')
                    ->log($status === Verification::STATUS_APPROVED
                        ? 'Menyetujui laporan krisis'
                        : 'Menolak laporan krisis');

                return redirect()
                    ->route('reports.show', $lockedReport)
                    ->with('status', $status === Verification::STATUS_APPROVED
                        ? 'Laporan berhasil disetujui.'
                        : 'Laporan berhasil ditolak.');
            });
        }

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
>>>>>>> 397304f4a2ec1792606c9f006b554a9b64ccaaa3
    }
}
