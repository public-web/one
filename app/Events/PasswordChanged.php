<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user who changed their password
     * @param bool $isFirstLogin Whether this was a first login password change
     */
    public function __construct(
        public User $user,
        public bool $isFirstLogin = false
    ) {}
}
