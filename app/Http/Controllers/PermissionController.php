<?php

namespace App\Http\Controllers;

use App\Events\PermissionCreated;
use App\Events\PermissionDeleted;
use App\Events\PermissionsDeleted;
use App\Events\PermissionUpdated;
use App\Http\Requests\DestroyManyPermissionsRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Traits\HandlesServiceExceptions;
use App\Models\Permission;
use App\Services\RolePermissionCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response as InertiaResponse;

class PermissionController extends Controller
{
    use HandlesServiceExceptions;
    /**
     * System permission prefixes that cannot be deleted or renamed
     */
    private const SYSTEM_PERMISSION_PREFIXES = ['users.', 'roles.', 'permissions.'];

    public function __construct(
        private RolePermissionCacheService $cacheService
    ) {}

    /**
     * Check if a permission is a system permission
     *
     * System permissions (users.*, roles.*, permissions.*) cannot be deleted or renamed
     * to protect core application functionality.
     *
     * @param Permission $permission The permission to check
     * @return bool True if the permission is a system permission, false otherwise
     */
    private function isSystemPermission(Permission $permission): bool
    {
        return collect(self::SYSTEM_PERMISSION_PREFIXES)
            ->contains(fn($prefix) => str_starts_with($permission->name, $prefix));
    }

    /**
     * Check if a permission can be deleted
     *
     * Validates that:
     * - The permission is not a system permission
     * - The permission is not assigned to any roles or users
     *
     * @param Permission $permission The permission to validate
     * @return string|null Error message if permission cannot be deleted, null if it can be deleted
     */
    private function canDeletePermission(Permission $permission): ?string
    {
        if ($this->isSystemPermission($permission)) {
            return 'No se pueden eliminar permisos del sistema';
        }

        if ($permission->roles()->exists() || $permission->users()->exists()) {
            return 'No se puede eliminar un permiso asignado a roles o usuarios';
        }

        return null;
    }

    /**
     * Display a listing of permissions with optional filters and pagination
     *
     * Supports filtering by:
     * - search: Search permissions by name
     * - category: Filter by permission category (e.g., "users", "roles")
     * - has_roles: Filter permissions assigned to roles
     * - has_users: Filter permissions assigned to users
     *
     * Supports pagination with 'per_page' and 'paginate' query parameters.
     *
     * @param Request $request The HTTP request containing filter and pagination parameters
     * @return JsonResponse|InertiaResponse JSON response for API calls, Inertia response for SPA
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     */
    public function index(Request $request): JsonResponse|InertiaResponse
    {
        $this->authorize('viewAny', Permission::class);

        // Build query with filters
        $query = Permission::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                // Filter by permission category (e.g., users.*, roles.*)
                $q->inCategory($request->category);
            })
            ->when($request->filled('has_roles'), function ($q) {
                $q->has('roles');
            })
            ->when($request->filled('has_users'), function ($q) {
                $q->has('users');
            })
            ->withCount('roles', 'users')
            ->orderBy('name');

        // Get all permissions (no pagination for now to simplify)
        $permissions = PermissionResource::collection($query->get())->resolve();

        // Extract permission categories for filtering
        $categories = Permission::categories();

        // Prepare response data
        $responseData = [
            'permissions' => $permissions,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'has_roles', 'has_users']),
        ];

        if ($request->wantsJson()) {
            return response()->json($responseData);
        }

        return inertia('Permissions/Index', $responseData);
    }

    /**
     * Store a newly created permission in the database
     *
     * Creates a new permission with the provided name and guard.
     * Permission names are automatically converted to lowercase and trimmed.
     * Invalidates the permissions cache after successful creation.
     *
     * @param StorePermissionRequest $request The validated request containing permission data
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If permission creation fails
     */
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);

        // Check for duplicate permission name after normalization
        $normalizedName = strtolower(trim($request->name));
        if (Permission::where('name', $normalizedName)->exists()) {
            return $this->validationErrorResponse('Ya existe un permiso con ese nombre');
        }

        try {
            $permission = DB::transaction(function () use ($normalizedName, $request) {
                // Create the permission
                $permission = Permission::create([
                    'name' => $normalizedName,
                    'guard_name' => $request->input('guard_name', 'web'),
                ]);

                return $permission;
            });

            // Dispatch event for cache invalidation (async via listener)
            PermissionCreated::dispatch($permission);

            return $this->successResponse('Permiso creado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error creating permission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->only(['name']),
            ]);

            return $this->errorResponse($this->friendlyError('crear el permiso', $e));
        }
    }

    /**
     * Update the specified permission in the database
     *
     * Updates permission name (lowercase and trimmed).
     * System permissions cannot be renamed to protect core functionality.
     * Guard name changes are not allowed for existing permissions.
     * Uses selective cache invalidation based on what changed.
     *
     * @param UpdatePermissionRequest $request The validated request containing updated permission data
     * @param Permission $permission The permission to update
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If permission update fails
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->authorize('update', $permission);

        // Check if name is changing (affects ordering/filtering/categories)
        $nameChanged = $permission->name !== strtolower(trim($request->name));

        // Prevent renaming system permissions
        if ($this->isSystemPermission($permission) && $nameChanged) {
            return $this->errorResponse('No se puede modificar el nombre de un permiso del sistema', false);
        }

        // Prevent changing guard_name of existing permission
        if ($request->filled('guard_name') && $request->guard_name !== $permission->guard_name) {
            return $this->errorResponse('No se puede cambiar el guardia de un permiso existente', false);
        }

        // Check for duplicate permission name after normalization
        $normalizedName = strtolower(trim($request->name));
        if ($nameChanged && Permission::where('name', $normalizedName)
            ->where('id', '!=', $permission->id)
            ->exists()) {
            return $this->validationErrorResponse('Ya existe un permiso con ese nombre');
        }

        try {
            DB::transaction(function () use ($permission, $normalizedName) {
                // Update permission name
                $permission->update([
                    'name' => $normalizedName,
                ]);
            });

            // Dispatch event for cache invalidation (async via listener)
            PermissionUpdated::dispatch($permission, $nameChanged);

            return $this->successResponse('Permiso actualizado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error updating permission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'permission_id' => $permission->id,
                'request_data' => $request->only(['name']),
            ]);

            return $this->errorResponse($this->friendlyError('actualizar el permiso', $e));
        }
    }

    /**
     * Remove the specified permission from the database
     *
     * System permissions cannot be deleted to protect core functionality.
     * Permissions assigned to roles or users cannot be deleted to prevent orphaned assignments.
     * Invalidates the permissions cache after successful deletion.
     *
     * @param Permission $permission The permission to delete
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If permission deletion fails
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $this->authorize('delete', $permission);

        // Validate if permission can be deleted
        if ($error = $this->canDeletePermission($permission)) {
            return $this->errorResponse($error, false);
        }

        try {
            $permissionId = $permission->id;
            $permissionName = $permission->name;

            DB::transaction(function () use ($permission) {
                $permission->delete();
            });

            // Dispatch event for cache invalidation (async via listener)
            PermissionDeleted::dispatch($permissionId, $permissionName);

            return $this->successResponse('Permiso eliminado exitosamente');
        } catch (\Throwable $e) {
            logger()->error('Error deleting permission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'permission_id' => $permissionId ?? null,
                'permission_name' => $permissionName ?? null,
            ]);

            return $this->errorResponse($this->friendlyError('eliminar el permiso', $e), false);
        }
    }

    /**
     * Get detailed information about a specific permission
     *
     * Returns permission data including roles count and users count.
     * Intended for API consumption or AJAX requests.
     *
     * @param Permission $permission The permission to retrieve
     * @return JsonResponse JSON response containing permission details
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     */
    public function show(Permission $permission): JsonResponse
    {
        $this->authorize('view', $permission);

        $permission->loadCount('roles', 'users');

        return response()->json([
            'permission' => new PermissionResource($permission),
        ]);
    }

    /**
     * Remove multiple permissions from the database in bulk
     *
     * Performs validation checks before deletion:
     * - Rejects system permissions to protect core functionality
     * - Rejects permissions assigned to roles or users
     * Invalidates the permissions cache after successful deletion.
     *
     * @param DestroyManyPermissionsRequest $request The validated request containing permission IDs
     * @return RedirectResponse Redirect back with success or error message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authorized
     * @throws \Throwable If bulk deletion fails
     */
    public function destroyMany(DestroyManyPermissionsRequest $request): RedirectResponse
    {
        $this->authorize('deleteMany', Permission::class);

        try {
            $ids = $request->input('ids', []);

            // Get all permissions to validate
            $permissionsToDelete = Permission::whereIn('id', $ids)->get();

            // Separate valid and invalid permissions
            $validPermissions = collect();
            $invalidPermissions = collect();

            foreach ($permissionsToDelete as $permission) {
                if ($this->canDeletePermission($permission) === null) {
                    $validPermissions->push($permission);
                } else {
                    $invalidPermissions->push($permission);
                }
            }

            // Delete valid permissions if any
            $deletedCount = 0;
            $deletedIds = [];
            if ($validPermissions->isNotEmpty()) {
                DB::transaction(function () use ($validPermissions, &$deletedCount, &$deletedIds) {
                    $deletedIds = $validPermissions->pluck('id')->toArray();
                    $deletedCount = Permission::whereIn('id', $deletedIds)->delete();
                });

                // Dispatch event for cache invalidation (async via listener)
                PermissionsDeleted::dispatch($deletedCount, $deletedIds);
            }

            // Build result message
            if ($invalidPermissions->isEmpty()) {
                // All permissions deleted successfully
                $message = "Se eliminaron exitosamente {$deletedCount} " . ($deletedCount === 1 ? 'permiso' : 'permisos');
                return $this->successResponse($message);
            }

            // Some permissions couldn't be deleted - provide detailed feedback
            $systemPermissions = $invalidPermissions->filter(fn($permission) =>
                $this->isSystemPermission($permission)
            );
            $assignedPermissions = $invalidPermissions->diff($systemPermissions);

            $message = '';
            if ($deletedCount > 0) {
                $message = "Se eliminaron {$deletedCount} " . ($deletedCount === 1 ? 'permiso' : 'permisos') . '. ';
            }

            $notDeleted = [];
            if ($systemPermissions->isNotEmpty()) {
                $notDeleted[] = 'Permisos del sistema: ' . $systemPermissions->pluck('name')->implode(', ');
            }
            if ($assignedPermissions->isNotEmpty()) {
                $notDeleted[] = 'Permisos asignados: ' . $assignedPermissions->pluck('name')->implode(', ');
            }

            $message .= 'No se pudieron eliminar: ' . implode(' | ', $notDeleted);

            // Use warning for partial success, error for complete failure
            return $deletedCount > 0
                ? $this->warningResponse($message)
                : $this->errorResponse($message, false);
        } catch (\Throwable $e) {
            logger()->error('Error deleting multiple permissions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'permission_ids' => $request->input('ids', []),
            ]);

            return $this->errorResponse($this->friendlyError('eliminar los permisos', $e), false);
        }
    }
}
