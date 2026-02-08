<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simple filtering
        $query = Activity::with('causer')
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(20)->withQueryString();
        
        $users = User::orderBy('name')->pluck('name', 'id');
        // Get distinct events avoiding empty ones if possible
        $events = Activity::select('event')
            ->distinct()
            ->whereNotNull('event')
            ->pluck('event');

        return view('pages.audit.index', compact('activities', 'users', 'events'));
    }
}
