<?php

namespace App\Providers;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Policies\CrisisReportPolicy;
use App\Policies\CrisisTypePolicy;
use App\Policies\RegionPolicy;
use App\Policies\UrgencyLevelPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policies([
            CrisisType::class => CrisisTypePolicy::class,
            UrgencyLevel::class => UrgencyLevelPolicy::class,
            Region::class => RegionPolicy::class,
            CrisisReport::class => CrisisReportPolicy::class,
        ]);
    }
}
