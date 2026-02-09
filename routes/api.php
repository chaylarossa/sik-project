<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:internal'])
    ->prefix('internal')
    ->group(function (): void {
        Route::get('ping', fn () => response()->json(['message' => 'pong']));

        Route::prefix('maps')->group(function () {
             Route::get('crisis-points', [App\Http\Controllers\Api\Internal\MapController::class, 'points'])
                ->name('api.internal.maps.points');
        });

        Route::prefix('dashboard')->group(function () {
             Route::get('summary', [App\Http\Controllers\Api\Internal\DashboardController::class, 'summary'])
                ->name('api.internal.dashboard.summary');
        });
    });
