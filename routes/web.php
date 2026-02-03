<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Http\Controllers\CrisisReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CrisisTypeController;
use App\Http\Controllers\Admin\UrgencyLevelController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read/{id?}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-read');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('role_or_permission:'.implode('|', [
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
            RoleName::Pimpinan->value,
            PermissionName::ViewDashboard->value,
        ]))
        ->name('dashboard');

    Route::view('/reports', 'pages.reports.index')
        ->middleware('permission:'.implode('|', [
            PermissionName::ViewReport->value,
            PermissionName::CreateReport->value,
            PermissionName::EditReport->value,
        ]))
        ->name('reports.index');

    Route::view('/verifications', 'pages.verifications.index')
        ->middleware('permission:'.PermissionName::VerifyReport->value)
        ->name('verifications.index');

    Route::view('/handling', 'pages.handling.index')
        ->middleware('permission:'.PermissionName::ManageHandling->value)
        ->name('handling.index');

    Route::view('/archive', 'pages.archive.index')
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.index');

    Route::get('/archive/export/pdf', [ExportController::class, 'exportPdf'])
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.export.pdf');

    Route::get('/archive/export/excel', [ExportController::class, 'archive'])
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.export.excel');

    Route::view('/audit-log', 'pages.audit.index')
        ->middleware('permission:'.PermissionName::ViewAuditLog->value)
        ->name('audit-log.index');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('permission:'.PermissionName::ManageMasterData->value)
        ->group(function () {
            Route::view('/master-data', 'pages.admin.master-data')->name('master-data');
            Route::resource('crisis-types', CrisisTypeController::class)->except(['show']);
            Route::resource('urgency-levels', UrgencyLevelController::class)->except(['show']);
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
