<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        // Get all roles with their permissions count
        $roles = Role::withCount('permissions', 'users')
            ->with('permissions:id,name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'permissions_count' => $role->permissions_count,
                    'users_count' => $role->users_count,
                    'permissions' => $role->permissions->pluck('name'),
                    'created_at' => $role->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get all available permissions
        $allPermissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => ucfirst(str_replace('.', ' ', $permission->name)),
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'roles' => $roles,
                'permissions' => $allPermissions,
            ]);
        }

        return inertia('Roles/Index', [
            'roles' => $roles,
            'permissions' => $allPermissions,
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Create the role
        $role = Role::create([
            'name' => strtolower(trim($request->name)),
            'guard_name' => 'web',
        ]);

        // Assign permissions to the role
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->back()->with('success', 'Rol creado exitosamente');
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Update role name
        $role->update([
            'name' => strtolower(trim($request->name)),
        ]);

        // Sync permissions (remove all old and add new ones)
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->back()->with('success', 'Rol actualizado exitosamente');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['superadmin', 'admin', 'user'])) {
            return redirect()->back()->with('error', 'No se pueden eliminar roles del sistema');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Rol eliminado exitosamente');
    }

    /**
     * Get role with permissions
     */
    public function show(Role $role)
    {
        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
                'users_count' => $role->users()->count(),
                'created_at' => $role->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Update permissions for a role
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Sync permissions
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->back()->with('success', 'Permisos del rol actualizados exitosamente');
    }
}
