<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $cards = $this->dashboard->cards();
        $typeChart = $this->dashboard->crisisTypeChart();
        $trend7 = $this->dashboard->dailyTrend(7);
        $trend30 = $this->dashboard->dailyTrend(30);
        $recentReports = $this->dashboard->recentReports();
        $pending = $this->dashboard->pendingVerifications();
        $operatorQueue = $this->dashboard->operatorQueue();
        $roleView = 'generic';

        if ($user->hasRole(RoleName::Administrator->value)) {
            $roleView = 'admin';
        }

        if ($user->hasRole(RoleName::OperatorLapangan->value)) {
            $roleView = 'operator';
        }

        if ($user->hasRole(RoleName::Verifikator->value)) {
            $roleView = 'verifikator';
        }

        if ($user->hasRole(RoleName::Pimpinan->value)) {
            $roleView = 'pimpinan';
        }

        return view('dashboard', [
            'roleView' => $roleView,
            'cards' => $cards,
            'typeChart' => $typeChart,
            'trend7' => $trend7,
            'trend30' => $trend30,
            'recentReports' => $recentReports,
            'pending' => $pending,
            'operatorQueue' => $operatorQueue,
        ]);
    }
}
