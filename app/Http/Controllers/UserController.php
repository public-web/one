<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportUsersRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\UserPermissionsResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\HandlesServiceExceptions;
use App\Models\User;
use App\Services\RolePermissionCacheService;
use App\Services\UserImportExportService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HandlesServiceExceptions;

    public function __construct(
        private UserService $userService,
        private UserImportExportService $importExportService,
        private RolePermissionCacheService $cacheService
    ) {}

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Build the query with scopes
        $query = User::query()
            ->withTrashed()
            ->with('roles')
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('role'), fn($q) => $q->byRole($request->role))
            ->when($request->filled('status'), fn($q) => $q->byStatus($request->status))
            ->when($request->filled('expiring'), fn($q) => $q->expiring($request->expiring));

        // Use simple pagination for better performance when total count not needed
        // Frontend can request simple pagination via ?simple=1 query parameter
        $perPage = $request->input('per_page', 15);
        $users = $request->boolean('simple')
            ? $query->simplePaginate($perPage)->withQueryString()
            : $query->paginate($perPage)->withQueryString();

        // Get roles from cache for better performance
        $roles = $this->cacheService->getRoles();

        if ($request->wantsJson()) {
            return response()->json([
                'users' => UserResource::collection($users),
                'availableRoles' => $roles,
                'filters' => $request->only(['search', 'role', 'status', 'expiring', 'per_page']),
            ]);
        }

        return inertia('Users/Index', [
            'users' => $users,
            'availableRoles' => $roles,
            'filters' => $request->only(['search', 'role', 'status', 'expiring', 'per_page']),
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        logger()->info('UserController::store called', [
            'auth_user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_data' => $request->validated(),
        ]);

        try {
            $user = $this->userService->createUser($request->validated());

            return redirect('/users')
                ->with('success', "Usuario '{$user->name}' creado exitosamente");
        } catch (\Throwable $e) {
            return $this->handleServiceError($e);
        }
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        logger()->info('UserController::update called', [
            'auth_user_id' => auth()->id(),
            'user_id' => $user->id,
            'request_method' => $request->method(),
            'request_data' => $request->validated(),
        ]);

        try {
            $this->userService->updateUser($user, $request->validated());

            return redirect()->back()
                ->with([
                    'success' => 'Usuario actualizado exitosamente',
                    'refresh' => true,  // Signal frontend to refresh data
                ]);
        } catch (\Throwable $e) {
            return $this->handleServiceError($e);
        }
    }

    /**
     * Soft delete the specified user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        return $this->executeServiceAction(
            fn() => $this->userService->deleteUser($user),
            'Usuario eliminado exitosamente (puede ser restaurado)'
        );
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('delete', $user);

        return $this->executeServiceAction(
            fn() => $this->userService->restoreUser($id),
            'Usuario restaurado exitosamente'
        );
    }

    /**
     * Permanently delete a soft-deleted user
     */
    public function forceDelete(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);

        return $this->executeServiceAction(
            fn() => $this->userService->forceDeleteUser($id),
            'Usuario eliminado permanentemente'
        );
    }

    /**
     * Get activity logs for a specific user
     *
     * Supports filtering by:
     * - event: Event type (created, updated, deleted, etc.)
     * - date_from: Start date (Y-m-d format)
     * - date_to: End date (Y-m-d format)
     * - causer_id: User who performed the action
     */
    public function activityLogs(User $user, Request $request)
    {
        $this->authorize('view', $user);

        // Build activity logs query with filters
        $query = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->with('causer')
            // Filter by event type (created, updated, deleted, etc.)
            ->when($request->filled('event'), function ($q) use ($request) {
                $q->where('event', $request->event);
            })
            // Filter by date range (from)
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            // Filter by date range (to)
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            })
            // Filter by causer (who performed the action)
            ->when($request->filled('causer_id'), function ($q) use ($request) {
                $q->where('causer_id', $request->causer_id);
            })
            ->orderBy('created_at', 'desc');

        // Paginate activity logs
        $perPage = $request->input('per_page', 15);
        $activities = $query->paginate($perPage)->withQueryString();

        // Get available event types for filter dropdown
        $availableEvents = \Spatie\Activitylog\Models\Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->distinct()
            ->pluck('event')
            ->sort()
            ->values();

        // Get users who have performed actions on this user (for causer filter)
        // Optimized: Single query with whereIn subquery to avoid N+1 problem
        $availableCausers = User::whereIn('id', function ($query) use ($user) {
            $query->select('causer_id')
                ->from('activity_log')
                ->where('subject_type', User::class)
                ->where('subject_id', $user->id)
                ->whereNotNull('causer_id')
                ->distinct();
        })
            ->select('id', 'name')
            ->get()
            ->map(fn($causer) => [
                'id' => $causer->id,
                'name' => $causer->name,
            ]);

        // Support both JSON and Inertia responses
        if ($request->wantsJson()) {
            return response()->json([
                'activities' => ActivityLogResource::collection($activities),
                'available_events' => $availableEvents,
                'available_causers' => $availableCausers,
                'filters' => $request->only(['event', 'date_from', 'date_to', 'causer_id', 'per_page']),
            ]);
        }

        return inertia('Users/ActivityLogs', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'activities' => ActivityLogResource::collection($activities),
            'availableEvents' => $availableEvents,
            'availableCausers' => $availableCausers,
            'filters' => $request->only(['event', 'date_from', 'date_to', 'causer_id', 'per_page']),
        ]);
    }

    /**
     * Get user permissions (both from roles and direct permissions)
     *
     * Returns two types of permissions:
     * - role_permissions: Inherited from assigned roles (read-only, cannot be modified here)
     * - direct_permissions: Explicitly assigned to this user (can be modified)
     *
     * Also returns the global list of all available permissions for UI selection.
     *
     * Frontend should display role_permissions with a badge/icon indicating they come from roles
     * and are not editable individually. Direct permissions should be clearly editable.
     *
     * Supports both JSON (API) and Inertia (SPA modal/page) responses
     */
    public function getPermissions(User $user, Request $request)
    {
        $this->authorize('view', $user);

        $permissionsData = new UserPermissionsResource($user);

        // Get all available permissions from cache for UI selection
        $availablePermissions = $this->cacheService->getPermissions(['id', 'name'])
            ->map(fn($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
            ]);

        // Support both JSON and Inertia responses
        if ($request->wantsJson()) {
            return response()->json([
                'user_permissions' => $permissionsData->toArray($request),
                'available_permissions' => $availablePermissions,
            ]);
        }

        // For Inertia SPA - useful for modals or dedicated permissions page
        return inertia('Users/Permissions', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'permissions' => $permissionsData->toArray($request),
            'availablePermissions' => $availablePermissions,
        ]);
    }

    /**
     * Sync direct permissions for a user
     *
     * This method only updates DIRECT permissions assigned to the user.
     * Role-based permissions are NOT affected and should be managed via roles.
     *
     * Use case: Grant specific permissions to a user without changing their role
     * Example: An 'editor' role user needs temporary access to 'users.delete'
     */
    public function syncDirectPermissions(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $this->userService->syncDirectPermissions($user, $request->input('permissions', []));

        return redirect()->back()
            ->with([
                'success' => 'Permisos directos actualizados exitosamente',
                'refresh' => true,  // Signal frontend to refresh user permissions
            ]);
    }

    /**
     * Export users to Excel/CSV
     *
     * By default, exports are queued for async processing (recommended for large datasets).
     * Use ?sync=1 query parameter for immediate download (only for small datasets).
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $format = $request->input('format', 'xlsx');
        $filters = $request->only(['search', 'role', 'status', 'expiring']);

        // Check if synchronous export is requested (for small exports)
        if ($request->boolean('sync')) {
            try {
                return $this->importExportService->exportUsers($format, $filters);
            } catch (\Throwable $e) {
                return $this->handleServiceError($e);
            }
        }

        // Create operation record for tracking
        $operation = \App\Models\UserImportExport::create([
            'user_id' => auth()->id(),
            'type' => \App\Enums\ImportExportType::Export,
            'status' => \App\Enums\ImportExportStatus::Pending,
            'format' => $format,
            'filters' => $filters,
        ]);

        // Queue the export job (recommended for better performance)
        \App\Jobs\Users\ExportUsersJob::dispatch($operation->id);

        return redirect()->back()
            ->with([
                'success' => 'La exportación se está procesando. Recibirás una notificación cuando esté lista.',
                'refresh' => false,  // No need to refresh, export is async
            ]);
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $this->authorize('create', User::class);

        try {
            return $this->importExportService->downloadImportTemplate();
        } catch (\Throwable $e) {
            return $this->handleServiceError($e);
        }
    }

    /**
     * Import users from Excel/CSV
     *
     * By default, imports are queued for async processing (recommended for large files).
     * Use ?sync=1 query parameter for immediate processing (only for small files <100 rows).
     */
    public function import(ImportUsersRequest $request)
    {
        // Check if synchronous import is requested (for small files)
        if ($request->boolean('sync')) {
            try {
                $result = $this->importExportService->importUsers($request->file('file'));

                if ($result['success']) {
                    return redirect()->back()
                        ->with('success', $result['message'])
                        ->with('import_details', [
                            'success_count' => $result['success_count'],
                            'error_count' => $result['error_count'],
                            'failures' => $result['failures'],
                            'errors' => $result['errors'],
                        ]);
                }

                return redirect()->back()
                    ->with('error', $result['message'])
                    ->with('import_failures', $result['failures']);
            } catch (\Throwable $e) {
                return $this->handleServiceError($e);
            }
        }

        // Store file temporarily for queue processing
        $file = $request->file('file');
        $path = $file->store('temp-imports');

        // Create operation record for tracking
        $operation = \App\Models\UserImportExport::create([
            'user_id' => auth()->id(),
            'type' => \App\Enums\ImportExportType::Import,
            'status' => \App\Enums\ImportExportStatus::Pending,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'format' => $file->getClientOriginalExtension(),
        ]);

        // Queue the import job (recommended for better performance)
        \App\Jobs\Users\ImportUsersJob::dispatch($operation->id);

        return redirect()->back()
            ->with([
                'success' => 'La importación se está procesando. Recibirás una notificación cuando termine.',
                'refresh' => false,  // No need to refresh yet, import is async
            ]);
    }

    /**
     * Get import/export operation history
     *
     * Shows all import and export operations with their status, results, and timing.
     * Supports filtering by:
     * - type: import or export
     * - status: pending, processing, completed, failed
     * - date_from: Start date
     * - date_to: End date
     * - trashed: null (exclude), 'with' (include), 'only' (only trashed)
     */
    public function importExportHistory(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Build query with filters
        $query = \App\Models\UserImportExport::query()
            ->with('user:id,name,email')
            ->trashed($request->input('trashed')) // Apply trashed filter
            ->when($request->filled('type'), fn($q) => $q->ofType($request->type))
            ->when($request->filled('status'), fn($q) => $q->withStatus($request->status))
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc');

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $operations = $query->paginate($perPage)->withQueryString();

        // Support both JSON and Inertia responses
        if ($request->wantsJson()) {
            return response()->json([
                'operations' => \App\Http\Resources\UserImportExportResource::collection($operations),
                'filters' => $request->only(['type', 'status', 'date_from', 'date_to', 'trashed', 'per_page']),
            ]);
        }

        return inertia('Users/ImportExportHistory', [
            'operations' => \App\Http\Resources\UserImportExportResource::collection($operations),
            'filters' => $request->only(['type', 'status', 'date_from', 'date_to', 'trashed', 'per_page']),
        ]);
    }

    /**
     * Archive (soft delete) an import/export operation
     *
     * Useful for cleaning up old operations from the main view
     * while maintaining audit trail
     */
    public function archiveOperation(int $id)
    {
        $this->authorize('viewAny', User::class);

        $operation = \App\Models\UserImportExport::findOrFail($id);

        return $this->executeServiceAction(
            fn() => $operation->delete(),
            'Operación archivada exitosamente',
            null,
            true,
            true
        );
    }

    /**
     * Restore an archived import/export operation
     */
    public function restoreOperation(int $id)
    {
        $this->authorize('viewAny', User::class);

        $operation = \App\Models\UserImportExport::withTrashed()->findOrFail($id);

        return $this->executeServiceAction(
            fn() => $operation->restore(),
            'Operación restaurada exitosamente',
            null,
            true,
            true
        );
    }

    /**
     * Permanently delete an archived operation
     *
     * WARNING: This action is irreversible
     * Should only be used for compliance or data retention policies
     */
    public function forceDeleteOperation(int $id)
    {
        $this->authorize('viewAny', User::class);

        $operation = \App\Models\UserImportExport::withTrashed()->findOrFail($id);

        return $this->executeServiceAction(
            fn() => $operation->forceDelete(),
            'Operación eliminada permanentemente',
            null,
            true,
            true
        );
    }
}
