<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRestored
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user that was restored
     * @param User|null $restoredBy The user who performed the restoration
     */
    public function __construct(
        public User $user,
        public ?User $restoredBy = null
    ) {}
}
