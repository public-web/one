<?php

namespace App\Http\Controllers;

use App\Events\PasswordChanged;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Traits\HandlesServiceExceptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PasswordChangeController extends Controller
{
    use HandlesServiceExceptions;

    /**
     * Show the password change form for first login
     *
     * Displays the password change interface for users who need to
     * change their password on first login.
     *
     * @return InertiaResponse
     */
    public function show(): InertiaResponse
    {
        return Inertia::render('Auth/PasswordChange', [
            'isFirstLogin' => true,
        ]);
    }

    /**
     * Handle password change request
     *
     * Updates the user's password and records the change timestamp.
     * Uses database transactions to ensure atomicity.
     * Dispatches PasswordChanged event for audit logging.
     *
     * @param UpdatePasswordRequest $request The validated request containing password data
     * @return RedirectResponse Redirect to dashboard with success or error message
     *
     * @throws \Throwable If password update fails
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isFirstLogin = $user->password_changed_at === null;

        try {
            // Update password and timestamp in transaction
            DB::transaction(fn () => $user->update([
                'password' => Hash::make($request->password),
                'password_changed_at' => now(),
            ]));

            // Dispatch event for audit logging (async via listener)
            PasswordChanged::dispatch($user, $isFirstLogin);

            // Better UX: show different message for first login
            $message = $isFirstLogin
                ? 'Bienvenido! Tu contraseña ha sido configurada exitosamente.'
                : 'Contraseña actualizada exitosamente.';

            return redirect()
                ->route('dashboard')
                ->with('status', $message);
        } catch (\Throwable $e) {
            logger()->error('Error updating password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
            ]);

            return $this->errorResponse(
                $this->friendlyError('actualizar la contraseña', $e),
                false
            );
        }
    }
}
