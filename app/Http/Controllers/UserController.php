<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        // Include soft-deleted users so they can be restored
        $users = User::withTrashed()->with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all(['id', 'name']);

        if (request()->wantsJson()) {
            return response()->json([
                'users' => $users,
                'availableRoles' => $roles,
            ]);
        }

        return inertia('Users/Index', [
            'users' => $users,
            'availableRoles' => $roles,
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

        return redirect()->back()->with('success', 'Usuario creado exitosamente');
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
}
