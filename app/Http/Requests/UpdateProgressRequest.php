<?php

namespace App\Http\Requests;

use App\Models\CrisisReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProgressRequest extends FormRequest
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
            'progress' => ['required', 'integer', 'between:0,100'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * Helper to get the related CrisisReport instance.
     */
    protected function getCrisisReport(): ?CrisisReport
    {
        $reportId = $this->input('crisis_report_id');
        return $reportId ? CrisisReport::with('handling')->find($reportId) : null;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $report = $this->getCrisisReport();

            if (! $report) {
                return; // 'exists' rule already handles this
            }

            $handling = $report->handling;

            if ($handling && $handling->isClosed()) {
                $validator->errors()->add(
                    'crisis_report_id',
                    'Laporan ini sudah DITUTUP (CLOSED) dan progress tidak dapat diperbarui.'
                );
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
            'progress.required' => 'Nilai progress wajib diisi.',
            'progress.integer' => 'Nilai progress harus berupa angka.',
            'progress.between' => 'Nilai progress harus antara 0 sampai 100.',
            'description.required' => 'Deskripsi progress wajib diisi.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 2000 karakter.',
        ];
    }
}
