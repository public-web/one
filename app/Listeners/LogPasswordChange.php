<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPasswordChange implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * Logs password changes for security audit purposes
     */
    public function handle(PasswordChanged $event): void
    {
        $context = $event->isFirstLogin ? 'first login' : 'password change';

        logger()->info('User password changed', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
