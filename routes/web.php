<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
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
        Route::get('/{id}/activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs');
    });

    // Future modules can be added here:
    // Route::prefix('contracts')->name('contracts.')->group(function () { ... });
    // Route::prefix('roles')->name('roles.')->group(function () { ... });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
