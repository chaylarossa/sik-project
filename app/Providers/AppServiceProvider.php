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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('internal', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
