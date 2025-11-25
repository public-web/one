<?php

namespace App\Events;

use App\Models\Role;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Role $role The role that was updated
     * @param bool $nameChanged Whether the role name was changed
     */
    public function __construct(
        public Role $role,
        public bool $nameChanged = false
    ) {}
}
