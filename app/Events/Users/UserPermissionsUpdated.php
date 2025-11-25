<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * UserPermissionsUpdated Event
 *
 * Dispatched when a user's direct permissions are synced/updated
 * Useful for auditing, logging, and notifications
 */
class UserPermissionsUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user whose permissions were updated
     * @param array $oldPermissions Previous direct permission names
     * @param array $newPermissions New direct permission names
     * @param User|null $updatedBy User who made the change
     */
    public function __construct(
        public User $user,
        public array $oldPermissions,
        public array $newPermissions,
        public ?User $updatedBy = null
    ) {}

    /**
     * Get permissions that were added
     */
    public function getAddedPermissions(): array
    {
        return array_values(array_diff($this->newPermissions, $this->oldPermissions));
    }

    /**
     * Get permissions that were removed
     */
    public function getRemovedPermissions(): array
    {
        return array_values(array_diff($this->oldPermissions, $this->newPermissions));
    }

    /**
     * Check if permissions actually changed
     */
    public function hasChanges(): bool
    {
        return count($this->getAddedPermissions()) > 0 || count($this->getRemovedPermissions()) > 0;
    }
}
