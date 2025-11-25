<?php

namespace App\Listeners\Users;

use App\Events\Users\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Send welcome notification when a user is created
 *
 * This listener can be extended to send welcome emails
 * or other notifications to newly created users.
 */
class SendUserWelcomeNotification implements ShouldQueue
{
    /**
     * Handle the user created event
     */
    public function handle(UserCreated $event): void
    {
        // TODO: Implement welcome email/notification
        // Example:
        // Mail::to($event->user)->send(new WelcomeEmail($event->user));

        logger()->info('Welcome notification queued for user', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
        ]);
    }
}
