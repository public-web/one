<?php

namespace App\Events;

use App\Models\Permission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Permission $permission The permission that was updated
     * @param bool $nameChanged Whether the permission name was changed
     */
    public function __construct(
        public Permission $permission,
        public bool $nameChanged = false
    ) {}
}
