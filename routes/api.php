<?php

use App\Http\Controllers\Api\BancoProyectoController;
use App\Http\Controllers\Api\PreviabilizacionSocialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Banco de Proyectos API Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\CheckPasswordChanged::class])->prefix('banco-proyectos')->name('api.banco-proyectos.')->group(function () {
    // Estadísticas
    Route::get('/stats', [BancoProyectoController::class, 'stats'])->name('stats');

    // Import/Export
    Route::get('/export', [BancoProyectoController::class, 'export'])->name('export');
    Route::get('/template', [BancoProyectoController::class, 'downloadTemplate'])->name('template');
    Route::post('/import', [BancoProyectoController::class, 'import'])->name('import');

    // Operaciones especiales
    Route::post('/{id}/restore', [BancoProyectoController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force', [BancoProyectoController::class, 'forceDelete'])->name('force-delete');

    // CRUD Resources
    Route::apiResource('', BancoProyectoController::class)->parameters([
        '' => 'proyecto'
    ]);
});

// Previabilización Social API Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\CheckPasswordChanged::class])->prefix('previabilizacion-social')->name('api.previabilizacion-social.')->group(function () {
    // Estadísticas
    Route::get('/stats', [PreviabilizacionSocialController::class, 'stats'])->name('stats');
});
