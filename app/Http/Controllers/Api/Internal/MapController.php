<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\CrisisReport;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function points(Request $request)
    {
        $this->authorize('viewAny', CrisisReport::class);
        
        // Dummy implementation for structure
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [],
        ]);
    }
}
