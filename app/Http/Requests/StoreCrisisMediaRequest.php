<?php

namespace App\Http\Requests;

use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreCrisisMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $this->user()?->can('uploadMedia', $report ?? CrisisReport::class) ?? false;
    }

    public function rules(): array
    {
        $maxFileSize = (int) env('UPLOAD_MAX_FILE_SIZE_KB', 20480);

        return [
            'files' => ['required', 'array', 'max:10'],
            'files.*' => [
                'file',
                'max:'.$maxFileSize,
                'mimes:jpg,jpeg,png,webp,mp4,pdf',
            ],
        ];
    }
}
