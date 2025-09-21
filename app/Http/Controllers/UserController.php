<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('users.list');

        $users = User::with('roles')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'users' => $users
            ]);
        }

        return inertia('Dashboard', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('users.create');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'active' => 'boolean',
            'require_2fa' => 'boolean',
            'role' => 'required|string|in:superadmin,admin,user',
        ]);

        // Generate a generic password for new users
        $genericPassword = 'Temporal123!';

        $user = User::create([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'password' => Hash::make($genericPassword),
            'password_changed_at' => null, // Force password change on first login
            'active' => $request->boolean('active', true),
            'require_2fa' => $request->boolean('require_2fa', false),
        ]);

        // Asignar rol al usuario
        $user->assignRole($request->role);

        // If 2FA is required, enable it automatically
        if ($request->boolean('require_2fa', false)) {
            $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                    return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
                })->toArray())),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return redirect()->back()->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('users.edit');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'active' => 'boolean',
            'require_2fa' => 'boolean',
            'role' => 'required|string|in:superadmin,admin,user',
        ]);

        $require2fa = $request->boolean('require_2fa', false);

        $user->update([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'active' => $request->boolean('active', true),
            'require_2fa' => $require2fa,
        ]);

        // Handle 2FA changes
        if ($require2fa && !$user->hasEnabledTwoFactorAuthentication()) {
            // If 2FA is being enabled, set it up automatically
            $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                    return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
                })->toArray())),
                'two_factor_confirmed_at' => now(),
            ])->save();
        } elseif (!$require2fa && $user->hasEnabledTwoFactorAuthentication()) {
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
        $this->authorize('users.delete');

        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente');
    }
}
