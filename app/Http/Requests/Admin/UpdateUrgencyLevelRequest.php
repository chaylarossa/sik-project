<?php

namespace App\Http\Requests\Admin;

use App\Enums\PermissionName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUrgencyLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PermissionName::ManageMasterData->value) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'level' => [
                'required',
                'integer',
                'between:1,5',
                Rule::unique('urgency_levels', 'level')->ignore($this->route('urgency_level')),
            ],
            'color' => ['nullable', 'string', 'max:20'],
            'is_high_priority' => ['nullable', 'boolean'],
        ];
    }
}
