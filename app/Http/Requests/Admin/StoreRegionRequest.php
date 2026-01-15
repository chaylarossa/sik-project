<?php

namespace App\Http\Requests\Admin;

use App\Enums\PermissionName;
use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PermissionName::ManageMasterData->value) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('regions', 'code'),
            ],
            'level' => ['required', 'string', Rule::in(Region::LEVELS)],
            'parent_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $this->validateHierarchy($validator);
        }];
    }

    protected function validateHierarchy(Validator $validator): void
    {
        $level = $this->string('level')->trim()->toString();
        $parentId = $this->filled('parent_id') ? (int) $this->input('parent_id') : null;
        $expectedParentLevel = Region::parentLevelFor($level);

        if ($expectedParentLevel === null && $parentId !== null) {
            $validator->errors()->add('parent_id', 'Level ini tidak memerlukan induk.');
            return;
        }

        if ($expectedParentLevel !== null && $parentId === null) {
            $validator->errors()->add('parent_id', 'Pilih induk sesuai level.');
            return;
        }

        if ($expectedParentLevel !== null && $parentId !== null) {
            $parent = Region::find($parentId);

            if (!$parent) {
                $validator->errors()->add('parent_id', 'Induk wilayah tidak ditemukan.');
                return;
            }

            if ($parent->level !== $expectedParentLevel) {
                $validator->errors()->add('parent_id', 'Induk harus berupa '.Region::labelForLevel($expectedParentLevel).'.');
            }
        }
    }
}
