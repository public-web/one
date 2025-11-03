<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Build the query
        $query = User::withTrashed()->with('roles');

        // Search filter (name or email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('active', true)->whereNull('deleted_at');
                    break;
                case 'inactive':
                    $query->where('active', false)->whereNull('deleted_at');
                    break;
                case 'deleted':
                    $query->onlyTrashed();
                    break;
            }
        }

        // Expiration filter
        if ($request->filled('expiring')) {
            if ($request->expiring === 'soon') {
                // Users expiring in the next 30 days
                $query->whereBetween('expires_at', [now(), now()->addDays(30)]);
            } elseif ($request->expiring === 'expired') {
                // Users already expired
                $query->where('expires_at', '<', now());
            }
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage)->withQueryString();

        $roles = \Spatie\Permission\Models\Role::all(['id', 'name']);

        if ($request->wantsJson()) {
            return response()->json([
                'users' => $users,
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

    public function store(Request $request)
    {
        \Log::info('UserController::store called', [
            'request_method' => $request->method(),
            'request_data' => $request->all(),
        ]);

        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'active' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'require_2fa' => 'boolean',
            'role' => 'required|string|in:superadmin,admin,user',
        ]);

        // Generate a temporary password for new users
        $temporaryPassword = config('app.default_password');

        $user = User::create([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'password' => $temporaryPassword, // Auto-hashed by 'hashed' cast
            'password_changed_at' => null, // Force password change on first login
            'active' => $request->boolean('active', true),
            'expires_at' => $request->expires_at,
            'require_2fa' => $request->boolean('require_2fa', false),
        ]);

        // Asignar rol al usuario
        $user->assignRole($request->role);

        // Send welcome email with temporary password
        $user->notify(new \App\Notifications\NewUserCreated($temporaryPassword));

        // If 2FA is required, enable it automatically
        if ($request->boolean('require_2fa', false)) {
            $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                    return \Illuminate\Support\Str::random(10).'-'.\Illuminate\Support\Str::random(10);
                })->toArray())),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        \Log::info('UserController::update called', [
            'user_id' => $user->id,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
        ]);

        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'active' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
            'require_2fa' => 'boolean',
            'role' => 'required|string|in:superadmin,admin,user',
        ]);

        $require2fa = $request->boolean('require_2fa', false);

        $user->update([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'active' => $request->boolean('active', true),
            'expires_at' => $request->expires_at,
            'require_2fa' => $require2fa,
        ]);

        // Handle 2FA changes
        if ($require2fa && ! $user->hasEnabledTwoFactorAuthentication()) {
            // If 2FA is being enabled, set it up automatically
            $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                    return \Illuminate\Support\Str::random(10).'-'.\Illuminate\Support\Str::random(10);
                })->toArray())),
                'two_factor_confirmed_at' => now(),
            ])->save();
        } elseif (! $require2fa && $user->hasEnabledTwoFactorAuthentication()) {
            // If 2FA is being disabled, clear the 2FA secrets
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();
        }

        // Actualizar rol del usuario
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete(); // Soft delete

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente (puede ser restaurado)');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('delete', $user); // Reuse delete permission for restore

        $user->restore();

        return redirect()->back()->with('success', 'Usuario restaurado exitosamente');
    }

    /**
     * Permanently delete a soft-deleted user.
     */
    public function forceDelete(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $user);

        $user->forceDelete();

        return redirect()->back()->with('success', 'Usuario eliminado permanentemente');
    }

    /**
     * Get activity logs for a specific user.
     */
    public function activityLogs(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('view', $user);

        $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', User::class)
            ->where('subject_id', $id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'properties' => $activity->properties,
                    'changes' => $activity->changes(),
                    'causer' => $activity->causer ? [
                        'id' => $activity->causer->id,
                        'name' => $activity->causer->name,
                    ] : null,
                    'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $activity->created_at->diffForHumans(),
                ];
            });

        return response()->json($activities);
    }

    /**
     * Get user permissions (both from roles and direct permissions)
     */
    public function getPermissions(User $user)
    {
        $this->authorize('view', $user);

        // Get all available permissions
        $allPermissions = \Spatie\Permission\Models\Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => ucfirst(str_replace('.', ' ', $permission->name)),
            ];
        });

        // Get permissions from roles
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');

        // Get direct permissions (not from roles)
        $directPermissions = $user->permissions()->pluck('name');

        return response()->json([
            'all_permissions' => $allPermissions,
            'role_permissions' => $rolePermissions,
            'direct_permissions' => $directPermissions,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->roles->pluck('name'),
            ],
        ]);
    }

    /**
     * Update user direct permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Sync direct permissions (this doesn't affect role permissions)
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->back()->with('success', 'Permisos actualizados exitosamente');
    }

    /**
     * Export users to Excel/CSV
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $format = $request->input('format', 'xlsx');
        $filters = $request->only(['search', 'role', 'status', 'expiring']);

        $filename = 'users_' . now()->format('Y-m-d_His') . '.' . $format;

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\UsersExport($filters),
            $filename
        );
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $this->authorize('create', User::class);

        $headers = ['name', 'email', 'role', 'active', 'require_2fa', 'expires_at'];
        $sample = [
            ['John Doe', 'john@example.com', 'user', 'true', 'false', '2026-12-31'],
            ['Jane Smith', 'jane@example.com', 'admin', 'true', 'true', ''],
        ];

        $export = new class($headers, $sample) implements \Maatwebsite\Excel\Concerns\FromArray {
            public function __construct(private $headers, private $sample) {}

            public function array(): array {
                return array_merge([$this->headers], $this->sample);
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download(
            $export,
            'users_import_template.xlsx'
        );
    }

    /**
     * Import users from Excel/CSV
     */
    public function import(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240', // Max 10MB
        ]);

        $import = new \App\Imports\UsersImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            $errors = $import->getErrors();

            $message = "Importación completada: {$successCount} usuario(s) creado(s) exitosamente.";

            if (count($failures) > 0 || count($errors) > 0) {
                $errorCount = count($failures) + count($errors);
                $message .= " {$errorCount} error(es) encontrado(s).";
            }

            return redirect()->back()->with('success', $message)->with('import_details', [
                'success_count' => $successCount,
                'error_count' => count($failures) + count($errors),
                'failures' => $failures,
                'errors' => $errors,
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return redirect()->back()->with('error', 'Error de validación en el archivo importado')->with('import_failures', $failures);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
