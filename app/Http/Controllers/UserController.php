<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
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
            'password' => ['required', Rules\Password::defaults()],
            'active' => 'boolean',
            'require_2fa' => 'boolean',
            'role' => 'required|string|in:superadmin,admin,user',
        ]);

        $user = User::create([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => $request->boolean('active', true),
            'require_2fa' => $request->boolean('require_2fa', false),
        ]);

        // Asignar rol al usuario
        $user->assignRole($request->role);

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

        $user->update([
            'name' => ucwords(strtolower(trim($request->name))),
            'email' => $request->email,
            'active' => $request->boolean('active', true),
            'require_2fa' => $request->boolean('require_2fa', false),
        ]);

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
