<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CrisisArchiveExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Collection $reports)
    {
    }

    public function collection(): Collection
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Nomor',
            'Waktu Kejadian',
            'Jenis',
            'Urgensi',
            'Wilayah',
            'Status Verifikasi',
            'Status Penanganan',
            'Ringkas Deskripsi',
        ];
    }

    /**
     * @param  \App\Models\CrisisReport|Arrayable  $report
     */
    public function map($report): array
    {
        return [
            $report->id,
            optional($report->occurred_at)->format('Y-m-d H:i'),
            $report->crisisType->name ?? '-',
            $report->urgencyLevel->name ?? '-',
            $report->region_id,
            $report->verification_status,
            $report->handling_status,
            str($report->description)->limit(120),
        ];
    }
}
