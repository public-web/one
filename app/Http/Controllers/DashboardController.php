<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Use the new helper method
        $canManageUsers = $user->isSuperAdmin();

        $roles = [];
        $users = [];

        if ($canManageUsers) {
            $roles = Role::all(['id', 'name']);

            // Include soft-deleted users for superadmins
            $users = User::withTrashed()
                ->with('roles')
                ->get();
        }

        return Inertia::render('Dashboard', [
            'canManageUsers' => $canManageUsers,
            'availableRoles' => $roles,
            'users' => $users,
        ]);
    }
}
