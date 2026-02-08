<?php

namespace App\Http\Requests;

use App\Enums\RoleName;
use App\Models\CrisisHandling;
use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'status' => ['required', Rule::in(CrisisHandling::ALLOWED_STATUSES)],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $reportId = $this->input('crisis_report_id');
            $newStatus = $this->input('status');

            if (! $reportId) {
                return;
            }

            $report = CrisisReport::with('handling')->find($reportId);
            if (! $report) {
                return;
            }

            $handling = $report->handling;
            $currentStatus = $handling?->status ?? CrisisHandling::STATUS_BARU;
            $currentProgress = $handling?->progress ?? 0;

            // 1. Jika current status == DITUTUP -> reject semua perubahan
            if ($currentStatus === CrisisHandling::STATUS_DITUTUP) {
                $validator->errors()->add('base', 'Laporan sudah DITUTUP dan status tidak dapat diubah lagi.');
                return;
            }

            // 2. Jika status target == SELESAI → progress harus 100
            if ($newStatus === CrisisHandling::STATUS_SELESAI && $currentProgress < 100) {
                $validator->errors()->add(
                    'status',
                    "Status tidak bisa diubah ke SELESAI karena progress masih {$currentProgress}%. Harap lengkapi progress menjadi 100%."
                );
            }

            // 3. Jika status target == DITUTUP → hanya role Admin/Koordinator (Pimpinan)
            if ($newStatus === CrisisHandling::STATUS_DITUTUP) {
                $user = $this->user();
                $isAllowed = $user->hasRole([RoleName::Administrator, RoleName::Pimpinan]); // Sesuaikan RoleName

                if (! $isAllowed) {
                    $validator->errors()->add('status', 'Hanya Administrator atau Pimpinan yang dapat mengubah status menjadi DITUTUP.');
                }
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'crisis_report_id.required' => 'Laporan krisis wajib dipilih.',
            'crisis_report_id.exists' => 'Laporan krisis yang dipilih tidak valid.',
            'status.required' => 'Status baru wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'note.max' => 'Catatan tidak boleh lebih dari 2000 karakter.',
        ];
    }
}
