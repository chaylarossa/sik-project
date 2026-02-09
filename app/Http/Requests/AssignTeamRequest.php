<?php

namespace App\Http\Requests;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class AssignTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(RoleName::Administrator->value)
            || $this->user()->can(PermissionName::ManageHandling->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'crisis_report_id' => ['required', 'exists:crisis_reports,id'],
            'unit_ids' => ['required', 'array', 'min:1'],
            'unit_ids.*' => ['exists:units,id'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'crisis_report_id.required' => 'Laporan krisis wajib dipilih.',
            'crisis_report_id.exists' => 'Laporan krisis yang dipilih tidak valid.',
            'unit_ids.required' => 'Minimal satu unit/tim harus dipilih.',
            'unit_ids.min' => 'Minimal satu unit/tim harus dipilih.',
            'unit_ids.*.exists' => 'Unit/tim yang dipilih tidak valid.',
            'note.max' => 'Catatan tidak boleh lebih dari 2000 karakter.',
        ];
    }
}
