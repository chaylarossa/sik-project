<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveFilterRequest;
use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(ArchiveFilterRequest $request)
    {
        $validated = $request->validated();

        $query = CrisisReport::with([
            'crisisType', 
            'urgencyLevel', 
            'region', 
            'creator'
        ]);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('crisis_type_id')) {
            $query->where('crisis_type_id', $request->crisis_type_id);
        }

        if ($request->filled('urgency_level_id')) {
            $query->where('urgency_level_id', $request->urgency_level_id);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->q . '%')
                  ->orWhere('address', 'like', '%' . $request->q . '%');
            });
        }

        $query->latest('occurred_at');

        $reports = $query->paginate($request->input('per_page', 15))
            ->withQueryString();

        $crisisTypes = CrisisType::orderBy('name')->get();
        $urgencyLevels = UrgencyLevel::orderBy('level')->get();
        // Hanya ambil region level provinsi (parent_id null) atau sesuaikan
        $regions = Region::whereNull('parent_id')->orderBy('name')->get();

        return view('pages.archive.index', compact('reports', 'crisisTypes', 'urgencyLevels', 'regions'));
    }
}
