<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        // Get all permissions with their roles count
        $permissions = Permission::withCount('roles', 'users')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => ucfirst(str_replace('.', ' ', $permission->name)),
                    'guard_name' => $permission->guard_name,
                    'roles_count' => $permission->roles_count,
                    'users_count' => $permission->users_count,
                    'created_at' => $permission->created_at->format('Y-m-d H:i:s'),
                ];
            });

        if ($request->wantsJson()) {
            return response()->json([
                'permissions' => $permissions,
            ]);
        }

        return inertia('Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        // Create the permission
        Permission::create([
            'name' => strtolower(trim($request->name)),
            'guard_name' => 'web',
        ]);

        return redirect()->back()->with('success', 'Permiso creado exitosamente');
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        // Update permission name
        $permission->update([
            'name' => strtolower(trim($request->name)),
        ]);

        return redirect()->back()->with('success', 'Permiso actualizado exitosamente');
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any role or user
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un permiso que está asignado a roles o usuarios');
        }

        $permission->delete();

        return redirect()->back()->with('success', 'Permiso eliminado exitosamente');
    }

    /**
     * Get permission details
     */
    public function show(Permission $permission)
    {
        return response()->json([
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => ucfirst(str_replace('.', ' ', $permission->name)),
                'guard_name' => $permission->guard_name,
                'roles_count' => $permission->roles()->count(),
                'users_count' => $permission->users()->count(),
                'created_at' => $permission->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
