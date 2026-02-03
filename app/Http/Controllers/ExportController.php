<?php

namespace App\Http\Controllers;

use App\Exports\CrisisArchiveExport;
use App\Models\CrisisReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function archive(Request $request)
    {
        $filters = [
            'crisis_type_id' => $request->input('crisis_type_id'),
            'verification_status' => $request->input('verification_status'),
            'handling_status' => $request->input('handling_status'),
            'region_id' => $request->input('region_id'),
            'period' => [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ];

        $reports = CrisisReport::with(['crisisType', 'urgencyLevel'])
            ->filter($filters)
            ->orderBy('occurred_at', 'desc')
            ->get();

        return Excel::download(new CrisisArchiveExport($reports), 'arsip-laporan.xlsx');
    }
}
