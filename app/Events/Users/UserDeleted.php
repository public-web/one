<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user that was soft-deleted
     * @param User|null $deletedBy The user who performed the deletion
     */
    public function __construct(
        public User $user,
        public ?User $deletedBy = null
    ) {}
}
