<?php

namespace App\Events\Users;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserForceDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $userId The ID of the user that was permanently deleted
     * @param string $userName The name of the user (for logging)
     * @param string $userEmail The email of the user (for logging)
     */
    public function __construct(
        public int $userId,
        public string $userName,
        public string $userEmail
    ) {}
}
