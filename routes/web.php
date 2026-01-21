<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Http\Controllers\CrisisReportController;
use App\Http\Controllers\Admin\CrisisTypeController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\UrgencyLevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HandlingAssignmentController;
use App\Http\Controllers\HandlingUpdateController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware('role_or_permission:'.implode('|', [
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
            RoleName::Pimpinan->value,
            PermissionName::ViewDashboard->value,
        ]))
        ->name('dashboard');

    Route::resource('reports', CrisisReportController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->names('reports')
        ->middleware('permission:'.implode('|', [
            PermissionName::ViewReport->value,
            PermissionName::CreateReport->value,
            PermissionName::EditReport->value,
        ]));

    Route::get('/reports/{report}/assignments', [HandlingAssignmentController::class, 'index'])
        ->name('reports.assignments.index')
        ->middleware('permission:'.PermissionName::ManageHandling->value);

    Route::post('/reports/{report}/assignments', [HandlingAssignmentController::class, 'store'])
        ->name('reports.assignments.store')
        ->middleware('permission:'.PermissionName::ManageHandling->value);

    Route::get('/reports/{report}/timeline', [HandlingUpdateController::class, 'index'])
        ->name('reports.timeline')
        ->middleware('permission:'.PermissionName::ManageHandling->value);

    Route::post('/reports/{report}/handling-updates', [HandlingUpdateController::class, 'store'])
        ->name('reports.updates.store')
        ->middleware('permission:'.PermissionName::ManageHandling->value);

    Route::get('/verifications', [VerificationController::class, 'index'])
        ->middleware('role_or_permission:'.implode('|', [
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
            PermissionName::VerifyReport->value,
        ]))
        ->name('verifications.index');

    Route::post('/verifications/{report}/approve', [VerificationController::class, 'approve'])
        ->middleware('role_or_permission:'.implode('|', [
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
            PermissionName::VerifyReport->value,
        ]))
        ->name('verifications.approve');

    Route::post('/verifications/{report}/reject', [VerificationController::class, 'reject'])
        ->middleware('role_or_permission:'.implode('|', [
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
            PermissionName::VerifyReport->value,
        ]))
        ->name('verifications.reject');

    Route::view('/handling', 'pages.handling.index')
        ->middleware('permission:'.PermissionName::ManageHandling->value)
        ->name('handling.index');

    Route::get('/archive', [App\Http\Controllers\ArchiveController::class, 'index'])
        ->middleware('permission:'.PermissionName::ExportData->value)
        ->name('archive.index');

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
            Route::resource('regions', RegionController::class)->except(['show']);
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
