<?php

namespace App\Http\Requests;

use App\Models\CrisisReport;
use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCrisisReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CrisisReport::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'crisis_type_id' => ['required', 'exists:crisis_types,id'],
            'urgency_level_id' => ['required', 'exists:urgency_levels,id'],
            'region_id' => [
                'required',
                Rule::exists('regions', 'id')->where('level', Region::LEVEL_VILLAGE),
            ],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string'],
        ];
    }
}
