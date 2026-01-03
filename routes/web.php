<?php

use App\Enums\PermissionName;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'permission:'.PermissionName::ViewDashboard->value])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/master-data', function () {
        return 'Master Data';
    })->middleware('permission:'.PermissionName::ManageMasterData->value)->name('admin.master-data');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
