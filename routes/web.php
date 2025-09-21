<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckPasswordChanged;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();
    $canManageUsers = $user->hasRole('superadmin');

    $roles = [];
    $users = [];
    if ($canManageUsers) {
        $roles = \Spatie\Permission\Models\Role::all(['id', 'name']);
        $users = \App\Models\User::with('roles')->get();
    }

    return Inertia::render('Dashboard', [
        'canManageUsers' => $canManageUsers,
        'availableRoles' => $roles,
        'users' => $users
    ]);
})->middleware(['auth', 'verified', CheckPasswordChanged::class])->name('dashboard');

Route::middleware(['auth', 'verified', CheckPasswordChanged::class])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
