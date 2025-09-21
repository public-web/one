<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form for first login
     */
    public function show()
    {
        return Inertia::render('auth/PasswordChange', [
            'isFirstLogin' => true
        ]);
    }

    /**
     * Handle password change request
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('La contraseña actual es incorrecta.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Contraseña actualizada exitosamente.');
    }
}
