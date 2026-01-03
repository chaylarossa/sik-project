<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('internal')
    ->group(function (): void {
        Route::get('ping', fn () => response()->json(['message' => 'pong']));
    });
