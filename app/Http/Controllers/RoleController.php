<?php

namespace App\Http\Controllers;

use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RolePermissionsUpdated;
use App\Events\RolesDeleted;
use App\Events\RoleUpdated;
use App\Http\Requests\DestroyManyRolesRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Traits\HandlesServiceExceptions;
use App\Models\Role;
use App\Services\RolePermissionCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response as InertiaResponse;

class RoleController extends Controller
{
    use HandlesServiceExceptions;
    /**
     * System roles that cannot be deleted or renamed
     */
    private const SYSTEM_ROLES = ['superadmin', 'admin', 'user'];

    public function __construct(
        private RolePermissionCacheService $cacheService
    ) {}

    /**
     * Check if a role can be deleted
     *
     * Validates that:
     * - The role is not a system role
     * - The role has no assigned users
     *
     * @param Role $role The role to validate
     * @return string|null Error message if role cannot be deleted, null if it can be deleted
     */
    private function canDeleteRole(Role $role): ?string
    {
        if (in_array($role->name, self::SYSTEM_ROLES)) {
            return 'No se pueden eliminar roles del sistema';
        }

        if ($role->users()->exists()) {
            return 'No se puede eliminar un rol con usuarios asignados';
        }

        return null;
    }

    /**
     * Display a listing of roles with optional filters and pagination
     *
     * Supports filtering by:
     * - search: Search roles by name
     * - has_permissions: Filter roles with assigned permissions
     * - has_users: Filter roles with assigned users
     *
     * Supports pagination with 'per_page' and 'paginate' query parameters.
     * Returns roles with their permissions and counts.
     *
     * @param Request $request The HTTP request containing filter and pagination parameters
     * @return JsonResponse|InertiaResponse JSON response for API calls, Inertia response for SPA
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     */
    public function index(Request $request): JsonResponse|InertiaResponse
    {
        $this->authorize('viewAny', Role::class);

        // Build query with filters
        $query = Role::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('has_permissions'), function ($q) {
                $q->has('permissions');
            })
            ->when($request->filled('has_users'), function ($q) {
                $q->has('users');
            })
            ->withCount('permissions', 'users')
            ->with('permissions:id,name')
            ->orderBy('name');

        // Get all roles (no pagination for now to simplify)
        $roles = RoleResource::collection($query->get())->resolve();

        // Get all available permissions from cache
        $allPermissions = PermissionResource::collection(
            $this->cacheService->getPermissions()
        )->resolve();

        // Prepare response data
        $responseData = [
            'roles' => $roles,
            'permissions' => $allPermissions,
            'filters' => $request->only(['search', 'has_permissions', 'has_users']),
        ];

        if ($request->wantsJson()) {
            return response()->json($responseData);
        }

        return inertia('Roles/Index', $responseData);
    }

    /**
     * Store a newly created role in the database
     *
     * Creates a new role with the provided name and guard.
     * Role names are automatically converted to lowercase and trimmed.
     * Optionally assigns permissions to the role during creation.
     * Invalidates the roles cache after successful creation.
     *
     * @param StoreRoleRequest $request The validated request containing role data
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If role creation fails
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        // Check for duplicate role name after normalization
        $normalizedName = strtolower(trim($request->name));
        if (Role::where('name', $normalizedName)->exists()) {
            return $this->validationErrorResponse('Ya existe un rol con ese nombre');
        }

        try {
            $role = DB::transaction(function () use ($normalizedName, $request) {
                // Create the role
                $role = Role::create([
                    'name' => $normalizedName,
                    'guard_name' => $request->input('guard_name', 'web'),
                ]);

                // Assign permissions to the role
                if ($request->has('permissions')) {
                    $role->givePermissionTo($request->permissions);
                }

                return $role;
            });

            // Dispatch event for cache invalidation (async via listener)
            RoleCreated::dispatch($role);

            return $this->successResponse('Rol creado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error creating role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->only(['name', 'permissions']),
            ]);

            return $this->errorResponse($this->friendlyError('crear el rol', $e));
        }
    }

    /**
     * Update the specified role in the database
     *
     * Updates role name (lowercase and trimmed) and syncs permissions.
     * System roles (superadmin, admin, user) cannot be renamed to protect core functionality.
     * Guard name changes are not allowed for existing roles.
     * Uses selective cache invalidation based on what changed.
     *
     * @param UpdateRoleRequest $request The validated request containing updated role data
     * @param Role $role The role to update
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If role update fails
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        // Check if name is changing
        $nameChanged = $role->name !== strtolower(trim($request->name));

        // Prevent renaming system roles
        if (in_array($role->name, self::SYSTEM_ROLES) && $nameChanged) {
            return $this->errorResponse('No se puede modificar el nombre de un rol del sistema', false);
        }

        // Prevent changing guard_name of existing role
        if ($request->filled('guard_name') && $request->guard_name !== $role->guard_name) {
            return $this->errorResponse('No se puede cambiar el guardia de un rol existente', false);
        }

        // Check for duplicate role name after normalization
        $normalizedName = strtolower(trim($request->name));
        if ($nameChanged && Role::where('name', $normalizedName)
            ->where('id', '!=', $role->id)
            ->exists()) {
            return $this->validationErrorResponse('Ya existe un rol con ese nombre');
        }

        try {
            DB::transaction(function () use ($role, $normalizedName, $request) {
                // Update role name
                $role->update([
                    'name' => $normalizedName,
                ]);

                // Sync permissions (remove all old and add new ones)
                if ($request->has('permissions')) {
                    $role->syncPermissions($request->permissions);
                } else {
                    $role->syncPermissions([]);
                }
            });

            // Dispatch event for cache invalidation (async via listener)
            RoleUpdated::dispatch($role, $nameChanged);

            return $this->successResponse('Rol actualizado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error updating role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'role_id' => $role->id,
                'request_data' => $request->only(['name', 'permissions']),
            ]);

            return $this->errorResponse($this->friendlyError('actualizar el rol', $e));
        }
    }

    /**
     * Remove the specified role from the database
     *
     * System roles (superadmin, admin, user) cannot be deleted to protect core functionality.
     * Roles with assigned users cannot be deleted to prevent orphaned user assignments.
     * Invalidates the roles cache after successful deletion.
     *
     * @param Role $role The role to delete
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If role deletion fails
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        // Validate if role can be deleted
        if ($error = $this->canDeleteRole($role)) {
            return $this->errorResponse($error, false);
        }

        try {
            $roleId = $role->id;
            $roleName = $role->name;

            DB::transaction(function () use ($role) {
                $role->delete();
            });

            // Dispatch event for cache invalidation (async via listener)
            RoleDeleted::dispatch($roleId, $roleName);

            return $this->successResponse('Rol eliminado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error deleting role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'role_id' => $roleId ?? null,
                'role_name' => $roleName ?? null,
            ]);

            return $this->errorResponse($this->friendlyError('eliminar el rol', $e), false);
        }
    }

    /**
     * Get detailed information about a specific role
     *
     * Returns role data including permissions and users count.
     * Intended for API consumption or AJAX requests.
     *
     * @param Role $role The role to retrieve
     * @return JsonResponse JSON response containing role details
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     */
    public function show(Role $role): JsonResponse
    {
        $this->authorize('view', $role);

        $role->loadCount('users')->load('permissions:id,name');

        return response()->json([
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Update permissions for a specific role
     *
     * Syncs the provided permissions with the role (removes old, adds new).
     * Uses selective cache invalidation (only invalidates this specific role).
     * Useful for bulk permission management separate from role name updates.
     *
     * @param UpdateRolePermissionsRequest $request The validated request containing permission names
     * @param Role $role The role to update permissions for
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If permission sync fails
     */
    public function updatePermissions(UpdateRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('syncPermissions', $role);

        try {
            DB::transaction(function () use ($role, $request) {
                // Sync permissions
                $role->syncPermissions($request->input('permissions', []));
            });

            // Dispatch event for cache invalidation (async via listener)
            RolePermissionsUpdated::dispatch($role);

            return $this->successResponse('Permisos del rol actualizados exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error updating role permissions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'role_id' => $role->id,
                'permissions' => $request->input('permissions', []),
            ]);

            return $this->errorResponse($this->friendlyError('actualizar los permisos del rol', $e), false);
        }
    }

    /**
     * Remove multiple roles from the database in bulk
     *
     * Performs validation checks before deletion:
     * - Rejects system roles (superadmin, admin, user) to protect core functionality
     * - Rejects roles with assigned users to prevent orphaned user assignments
     * Invalidates the roles cache after successful deletion.
     *
     * @param DestroyManyRolesRequest $request The validated request containing role IDs
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If bulk deletion fails
     */
    public function destroyMany(DestroyManyRolesRequest $request): RedirectResponse
    {
        $this->authorize('deleteMany', Role::class);

        try {
            $ids = $request->input('ids', []);

            // Get all roles to validate
            $rolesToDelete = Role::whereIn('id', $ids)->get();

            // Separate valid and invalid roles
            $validRoles = collect();
            $invalidRoles = collect();

            foreach ($rolesToDelete as $role) {
                if ($this->canDeleteRole($role) === null) {
                    $validRoles->push($role);
                } else {
                    $invalidRoles->push($role);
                }
            }

            // Delete valid roles if any
            $deletedCount = 0;
            $deletedIds = [];
            if ($validRoles->isNotEmpty()) {
                DB::transaction(function () use ($validRoles, &$deletedCount, &$deletedIds) {
                    $deletedIds = $validRoles->pluck('id')->toArray();
                    $deletedCount = Role::whereIn('id', $deletedIds)->delete();
                });

                // Dispatch event for cache invalidation (async via listener)
                RolesDeleted::dispatch($deletedCount, $deletedIds);
            }

            // Build result message
            if ($invalidRoles->isEmpty()) {
                // All roles deleted successfully
                $message = "Se eliminaron exitosamente {$deletedCount} " . ($deletedCount === 1 ? 'rol' : 'roles');
                return $this->successResponse($message);
            }

            // Some roles couldn't be deleted - provide detailed feedback
            $systemRoles = $invalidRoles->filter(fn($role) =>
                in_array($role->name, self::SYSTEM_ROLES)
            );
            $rolesWithUsers = $invalidRoles->diff($systemRoles);

            $message = '';
            if ($deletedCount > 0) {
                $message = "Se eliminaron {$deletedCount} " . ($deletedCount === 1 ? 'rol' : 'roles') . '. ';
            }

            $notDeleted = [];
            if ($systemRoles->isNotEmpty()) {
                $notDeleted[] = 'Roles del sistema: ' . $systemRoles->pluck('name')->implode(', ');
            }
            if ($rolesWithUsers->isNotEmpty()) {
                $notDeleted[] = 'Roles con usuarios: ' . $rolesWithUsers->pluck('name')->implode(', ');
            }

            $message .= 'No se pudieron eliminar: ' . implode(' | ', $notDeleted);

            // Use warning for partial success, error for complete failure
            return $deletedCount > 0
                ? $this->warningResponse($message)
                : $this->errorResponse($message, false);
        } catch (\Throwable $e) {
            logger()->error('Error deleting multiple roles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'role_ids' => $request->input('ids', []),
            ]);

            return $this->errorResponse($this->friendlyError('eliminar los roles', $e), false);
        }
    }
}
