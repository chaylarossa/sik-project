<?php

namespace App\Http\Requests;

use App\Enums\RoleName;
use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHandlingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof CrisisReport
            && ($this->user()?->can('updateProgress', $report) ?? false);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(CrisisReport::STATUSES)],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $report = $this->route('report');

            if (! $report instanceof CrisisReport) {
                return;
            }

            $newStatus = $this->input('status');
            $isAdmin = $this->user()?->hasRole(RoleName::Administrator->value) ?? false;

            if (! $newStatus || $isAdmin) {
                return;
            }

            $latestStatus = $report->handlingUpdates()
                ->latest('occurred_at')
                ->latest('id')
                ->value('status') ?? $report->status;

            if ($this->isStatusBackward($newStatus, $latestStatus)) {
                $validator->errors()->add('status', 'Status tidak boleh mundur dari progres terakhir.');
            }
        });
    }

    private function isStatusBackward(string $newStatus, string $currentStatus): bool
    {
        $order = [
            CrisisReport::STATUS_NEW => 1,
            CrisisReport::STATUS_IN_PROGRESS => 2,
            CrisisReport::STATUS_DONE => 3,
            CrisisReport::STATUS_CLOSED => 4,
        ];

        return ($order[$newStatus] ?? 0) < ($order[$currentStatus] ?? 0);
    }
}
