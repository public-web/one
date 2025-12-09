<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Use the new helper method
        $canManageUsers = $user->isSuperAdmin();
        $canManageBancoProyectos = $user->hasAnyRole(['superadmin', 'banco-proyectos']);
        $canManagePreviabilizacion = $user->hasRole('previabilizacion-social') && !$user->hasRole('superadmin');

        $roles = [];
        $statistics = [];
        $recentUsers = [];
        $expiringUsers = [];
        $roleDistribution = [];

        if ($canManageUsers) {
            $roles = Role::all(['id', 'name']);

            // Calculate statistics
            $allUsers = User::withTrashed()->get();
            $activeUsers = User::where('active', true)->whereNull('deleted_at')->get();
            $expiredUsers = User::whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->whereNull('deleted_at')
                ->get();
            $users2FA = User::where('require_2fa', true)->whereNull('deleted_at')->get();

            $statistics = [
                'total' => $allUsers->count(),
                'active' => $activeUsers->count(),
                'expired' => $expiredUsers->count(),
                'with2FA' => $users2FA->count(),
                'inactive' => User::where('active', false)->whereNull('deleted_at')->count(),
                'deleted' => User::onlyTrashed()->count(),
            ];

            // Get recent users (last 5)
            $recentUsers = User::with('roles')
                ->latest()
                ->take(5)
                ->get();

            // Get users expiring in the next 7 days
            $expiringUsers = User::whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('active', true)
                ->whereNull('deleted_at')
                ->with('roles')
                ->orderBy('expires_at', 'asc')
                ->get();

            // Role distribution for chart
            $roleDistribution = Role::withCount('users')->get()->map(function ($role) {
                return [
                    'name' => ucfirst($role->name),
                    'count' => $role->users_count,
                ];
            });
        }

        return Inertia::render('Dashboard', [
            'canManageUsers' => $canManageUsers,
            'canManageBancoProyectos' => $canManageBancoProyectos,
            'canManagePreviabilizacion' => $canManagePreviabilizacion,
            'availableRoles' => $roles,
            'statistics' => $statistics,
            'recentUsers' => $recentUsers,
            'expiringUsers' => $expiringUsers,
            'roleDistribution' => $roleDistribution,
        ]);
    }
}
