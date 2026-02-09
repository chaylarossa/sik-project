<?php

namespace App\Http\Controllers;

use App\Enums\PermissionName;
use App\Models\CrisisHandling;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\CrisisReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request): View
    {
        if (! $request->user()->can(PermissionName::ViewMaps->value)) {
            abort(403);
        }

        return view('maps.index', [
            'crisisTypes' => CrisisType::query()->orderBy('name')->get(['id', 'name']),
            'regions' => Region::query()
                ->where('level', Region::LEVEL_PROVINCE)
                ->select('name', 'id')
                ->orderBy('name')
                ->get(),
            'handlingStatuses' => CrisisHandling::ALLOWED_STATUSES,
            'statusOptions' => CrisisReport::STATUSES,
        ]);
    }
}
