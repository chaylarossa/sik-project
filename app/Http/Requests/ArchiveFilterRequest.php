<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'crisis_type_id' => ['nullable', 'exists:crisis_types,id'],
            'urgency_level_id' => ['nullable', 'exists:urgency_levels,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'verification_status' => ['nullable', 'string', 'in:pending,approved,rejected'],
            'status' => ['nullable', 'string', 'in:new,in_progress,done,closed'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
