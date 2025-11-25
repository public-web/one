<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionsDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $deletedCount The number of permissions that were deleted
     * @param array $deletedIds The IDs of the permissions that were deleted
     */
    public function __construct(
        public int $deletedCount,
        public array $deletedIds
    ) {}
}
