<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $permissionId The ID of the permission that was deleted
     * @param string $permissionName The name of the permission that was deleted
     */
    public function __construct(
        public int $permissionId,
        public string $permissionName
    ) {}
}
