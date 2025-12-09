<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BancoProyectoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PreviabilizacionSocialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckPasswordChanged;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
})->name('home');

// Protected routes - require authentication, verification, and password change
Route::middleware(['auth', 'verified', CheckPasswordChanged::class])->group(function () {
    // Dashboard
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Previabilización Social Dashboard
    Route::get('previabilizacion-social/dashboard', [PreviabilizacionSocialController::class, 'dashboard'])
        ->name('previabilizacion-social.dashboard')
        ->middleware('role:previabilizacion-social|superadmin');

    // Activity Logs (System-wide)
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Users management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::match(['put', 'post'], '/{user}', [UserController::class, 'update'])->name('update')->withTrashed();
        Route::match(['delete', 'post'], '/{user}', [UserController::class, 'destroy'])->name('destroy')->withTrashed();
        Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [UserController::class, 'forceDelete'])->name('force-delete');
        Route::get('/{user}/activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs')->withTrashed();

        // Permissions management
        Route::get('/{user}/permissions', [UserController::class, 'getPermissions'])->name('permissions.get');
        Route::post('/{user}/permissions', [UserController::class, 'syncDirectPermissions'])->name('permissions.sync');

        // Import/Export
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
        Route::post('/import', [UserController::class, 'import'])->name('import');
        Route::get('/import-export-history', [UserController::class, 'importExportHistory'])->name('import-export.history');
        Route::delete('/operations/{id}', [UserController::class, 'archiveOperation'])->name('operations.archive');
        Route::post('/operations/{id}/restore', [UserController::class, 'restoreOperation'])->name('operations.restore');
        Route::delete('/operations/{id}/force', [UserController::class, 'forceDeleteOperation'])->name('operations.force-delete');
    });

    // Roles management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::post('/delete-many', [RoleController::class, 'destroyMany'])->name('destroy-many');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::match(['put', 'post'], '/{role}', [RoleController::class, 'update'])->name('update');
        Route::match(['delete', 'post'], '/{role}/delete', [RoleController::class, 'destroy'])->name('destroy');
        Route::post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
    });

    // Permissions management
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::post('/delete-many', [PermissionController::class, 'destroyMany'])->name('destroy-many');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
        Route::match(['put', 'post'], '/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::match(['delete', 'post'], '/{permission}/delete', [PermissionController::class, 'destroy'])->name('destroy');
    });

    // Banco de Proyectos - Accesible para usuarios con rol 'banco-proyectos', 'previabilizacion-social' o 'superadmin'
    Route::prefix('banco-proyectos')->name('banco-proyectos.')->middleware('role:banco-proyectos|previabilizacion-social|superadmin')->group(function () {
        Route::get('/', [BancoProyectoController::class, 'index'])->name('index');
        Route::get('/mapa', [BancoProyectoController::class, 'map'])->name('mapa');
        Route::get('/{id}', [BancoProyectoController::class, 'show'])->name('show');

        // Detalle de Proyectos CRUD
        Route::post('/{proyectoId}/detalles', [BancoProyectoController::class, 'storeDetalle'])->name('detalles.store');
        Route::match(['put', 'post'], '/{proyectoId}/detalles/{detalleId}', [BancoProyectoController::class, 'updateDetalle'])->name('detalles.update');
        Route::match(['delete', 'post'], '/{proyectoId}/detalles/{detalleId}/delete', [BancoProyectoController::class, 'destroyDetalle'])->name('detalles.destroy');

        // Delete individual document
        Route::delete('/{proyectoId}/detalles/{detalleId}/documentos/{documentoId}', [BancoProyectoController::class, 'destroyDocumento'])->name('detalles.documentos.destroy');

        // Previabilización Social CRUD
        Route::post('/{proyectoId}/previabilizaciones', [BancoProyectoController::class, 'storePreviabilizacion'])->name('previabilizaciones.store');
        Route::match(['put', 'post'], '/{proyectoId}/previabilizaciones/{previabilizacionId}', [BancoProyectoController::class, 'updatePreviabilizacion'])->name('previabilizaciones.update');
        Route::match(['delete', 'post'], '/{proyectoId}/previabilizaciones/{previabilizacionId}/delete', [BancoProyectoController::class, 'destroyPreviabilizacion'])->name('previabilizaciones.destroy');
    });

    // Future modules can be added here:
    // Route::prefix('contracts')->name('contracts.')->group(function () { ... });
});

// Artículos (para testing) - Solo requiere auth y verified (sin CheckPasswordChanged)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('articulos', \App\Http\Controllers\ArticuloController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
