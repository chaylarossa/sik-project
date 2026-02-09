<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCrisisMediaRequest;
use App\Models\CrisisMedia;
use App\Models\CrisisReport;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;

class CrisisMediaController extends Controller
{
    public function __construct(private readonly MediaUploadService $mediaService)
    {
    }

    public function store(StoreCrisisMediaRequest $request, CrisisReport $report): RedirectResponse
    {
        $this->authorize('uploadMedia', $report);

        $files = $request->file('files', []);

        $this->mediaService->upload($report, $files, $request->user());

        return back()->with('status', 'Media berhasil diunggah.');
    }

    public function destroy(CrisisReport $report, CrisisMedia $media): RedirectResponse
    {
        $this->authorize('deleteMedia', $report);

        if ($media->crisis_report_id !== $report->id) {
            abort(404);
        }

        $this->mediaService->delete($media);

        return back()->with('status', 'Media berhasil dihapus.');
    }
}
