<?php

namespace App\Http\Controllers\Api\Internal;

use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        // Enforce permission check
        if (! $request->user()->can(PermissionName::ViewDashboard->value) && 
            ! $request->user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'stats' => [
                'total_reports' => 0,
                'pending_verification' => 0,
            ]
        ]);
    }
}
