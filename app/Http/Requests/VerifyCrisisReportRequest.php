<?php

namespace App\Http\Requests;

use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyCrisisReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $this->user()?->can('verify', $report ?? CrisisReport::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'note' => [
                'nullable',
                'string',
                'max:1000',
                Rule::requiredIf(fn () => $this->input('action') === 'reject'),
            ],
        ];
    }
}
