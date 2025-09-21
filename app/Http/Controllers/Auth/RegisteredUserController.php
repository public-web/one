<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        // Generate a generic password for new users
        $genericPassword = 'Temporal123!';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($genericPassword),
            'password_changed_at' => null, // Force password change on first login
        ]);

        event(new Registered($user));

        // Don't auto-login the user, redirect to login with message
        return redirect()->route('login')->with('status', 'Usuario registrado exitosamente. Use la contraseña temporal: ' . $genericPassword);
    }
}
