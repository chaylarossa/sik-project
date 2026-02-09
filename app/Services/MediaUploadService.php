<?php

namespace App\Services;

use App\Models\CrisisMedia;
use App\Models\CrisisReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    /**
     * @param  UploadedFile[]  $files
     * @return Collection<int, CrisisMedia>
     */
    public function upload(CrisisReport $report, array $files, User $user): Collection
    {
        $saved = collect();

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension() ?: $file->extension();
            $filename = Str::uuid()->toString().($extension ? '.'.$extension : '');
            $path = 'crisis/'.$report->id.'/'.$filename;

            Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );

            $saved->push(CrisisMedia::create([
                'crisis_report_id' => $report->id,
                'uploaded_by' => $user->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType() ?? $file->getMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize(),
            ]));
        }

        return $saved;
    }

    public function delete(CrisisMedia $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }
}
