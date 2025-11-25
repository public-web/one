<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $roleId The ID of the role that was deleted
     * @param string $roleName The name of the role that was deleted
     */
    public function __construct(
        public int $roleId,
        public string $roleName
    ) {}
}
