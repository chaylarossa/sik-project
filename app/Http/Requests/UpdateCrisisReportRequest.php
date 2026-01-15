<?php

namespace App\Http\Requests;

use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCrisisReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $this->user()?->can('update', $report ?? CrisisReport::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'crisis_type_id' => ['sometimes', 'required', 'exists:crisis_types,id'],
            'urgency_level_id' => ['sometimes', 'required', 'exists:urgency_levels,id'],
            'region_id' => ['sometimes', 'required', 'exists:regions,id'],
            'occurred_at' => ['sometimes', 'required', 'date', 'before_or_equal:now'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'description' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', Rule::in(CrisisReport::STATUSES)],
        ];
    }
}
