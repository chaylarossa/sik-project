<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Http\Controllers\CrisisReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CrisisMediaController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Admin\CrisisTypeController;
use App\Http\Controllers\Admin\UrgencyLevelController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\VerificationController;
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

    Route::resource('reports', CrisisReportController::class);

    Route::post('/reports/{report}/media', [CrisisMediaController::class, 'store'])
        ->name('reports.media.store');

    Route::delete('/reports/{report}/media/{media}', [CrisisMediaController::class, 'destroy'])
        ->name('reports.media.destroy');

    Route::get('/reports/{report}/verify', [VerificationController::class, 'create'])
        ->middleware('permission:'.PermissionName::VerifyReport->value)
        ->name('reports.verify');

    Route::post('/reports/{report}/verify', [VerificationController::class, 'store'])
        ->middleware('permission:'.PermissionName::VerifyReport->value)
        ->name('reports.verify.store');
    Route::get('/verifications', [VerificationController::class, 'index'])
        ->middleware('permission:'.PermissionName::VerifyReport->value)
        ->name('verifications.index');

    Route::prefix('penanganan')
        ->name('handling.')
        ->middleware(['auth', 'permission:'.PermissionName::ManageHandling->value])
        ->controller(App\Http\Controllers\HandlingController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{crisisReport}', 'show')->name('show');
            Route::get('/{crisisReport}/timeline', 'timeline')->name('timeline');
            Route::post('/assign', 'assignTeam')->name('assign'); // crisis_report_id di body
            Route::post('/progress', 'updateProgress')->name('progress');
            Route::post('/status', 'changeStatus')->name('status');
        });

    Route::get('/maps', [MapController::class, 'index'])
        ->middleware('permission:'.PermissionName::ViewMaps->value)
        ->name('maps.index');

    Route::view('/archive', 'pages.archive.index')
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.index');

    Route::get('/archive/export/pdf', [ExportController::class, 'exportPdf'])
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.export.pdf');

    Route::get('/archive/export/excel', [ExportController::class, 'archive'])
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.export.excel');

    Route::get('/audit-log', [App\Http\Controllers\AuditLogController::class, 'index'])
        ->middleware('permission:'.PermissionName::ViewAuditLog->value)
        ->name('audit-log.index');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('permission:'.PermissionName::ManageMasterData->value)
        ->group(function () {
            Route::view('/master-data', 'pages.admin.master-data')->name('master-data');
            Route::resource('crisis-types', CrisisTypeController::class)->except(['show']);
            Route::resource('urgency-levels', UrgencyLevelController::class)->except(['show']);
            Route::resource('units', UnitController::class)->except(['show']);
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

