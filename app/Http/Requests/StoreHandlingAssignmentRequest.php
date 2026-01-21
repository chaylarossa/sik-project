<?php

namespace App\Http\Requests;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\HandlingAssignment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHandlingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof CrisisReport
            && ($this->user()?->can('assign', $report) ?? false);
    }

    public function rules(): array
    {
        return [
            'unit_id' => ['required', 'exists:units,id'],
            'assignee_id' => ['required', 'exists:users,id'],
            'status' => ['nullable', Rule::in(HandlingAssignment::STATUSES)],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $report = $this->route('report');

            if (! $report instanceof CrisisReport) {
                return;
            }

            if ($report->verification_status !== CrisisReport::VERIFICATION_APPROVED) {
                $validator->errors()->add('report_id', 'Laporan belum disetujui untuk penugasan.');
            }

            $assigneeId = $this->input('assignee_id');
            if ($assigneeId) {
                $assignee = User::query()->with('roles')->find($assigneeId);
                $isPublik = $assignee?->roles?->contains('name', RoleName::Publik->value) ?? false;

                if (! $assignee || $isPublik) {
                    $validator->errors()->add('assignee_id', 'Assignee harus merupakan user internal aktif.');
                }
            }

            $unitId = $this->input('unit_id');
            $status = $this->input('status', HandlingAssignment::STATUS_ACTIVE);

            if ($unitId && $status === HandlingAssignment::STATUS_ACTIVE) {
                $duplicateActive = HandlingAssignment::query()
                    ->where('report_id', $report->id)
                    ->where('unit_id', $unitId)
                    ->where('status', HandlingAssignment::STATUS_ACTIVE)
                    ->exists();

                if ($duplicateActive) {
                    $validator->errors()->add('unit_id', 'Unit sudah memiliki penugasan aktif pada laporan ini.');
                }
            }
        });
    }
}
