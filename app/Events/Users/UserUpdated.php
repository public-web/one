<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user that was updated
     * @param array $changes The changes made to the user
     * @param User|null $updatedBy The user who made the update
     */
    public function __construct(
        public User $user,
        public array $changes = [],
        public ?User $updatedBy = null
    ) {}
}
