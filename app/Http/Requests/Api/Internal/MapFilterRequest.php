<?php

namespace App\Http\Requests\Api\Internal;

use App\Models\CrisisHandling;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MapFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'crisis_type_id' => ['nullable', 'integer', 'exists:crisis_types,id'],
            'handling_status' => ['nullable', Rule::in(CrisisHandling::ALLOWED_STATUSES)],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }
}
