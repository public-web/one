<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
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

        // Generate a temporary password for new users
        $temporaryPassword = config('app.default_password');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $temporaryPassword, // Auto-hashed by 'hashed' cast
            'password_changed_at' => null, // Force password change on first login
        ]);

        event(new Registered($user));

        // Send welcome email with temporary password
        $user->notify(new \App\Notifications\NewUserCreated($temporaryPassword));

        // Don't auto-login the user, redirect to login with message
        return redirect()->route('login')->with('status', 'Usuario registrado exitosamente. Revisa tu correo electrónico para obtener tu contraseña temporal.');
    }
}
