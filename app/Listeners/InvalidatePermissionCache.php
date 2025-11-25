<?php

namespace App\Listeners;

use App\Events\PermissionCreated;
use App\Events\PermissionDeleted;
use App\Events\PermissionsDeleted;
use App\Events\PermissionUpdated;
use App\Services\RolePermissionCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidatePermissionCache implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private RolePermissionCacheService $cacheService
    ) {}

    /**
     * Handle permission created event.
     */
    public function handlePermissionCreated(PermissionCreated $event): void
    {
        $this->cacheService->invalidatePermissionsCache();
    }

    /**
     * Handle permission updated event.
     */
    public function handlePermissionUpdated(PermissionUpdated $event): void
    {
        if ($event->nameChanged) {
            // Name changed: invalidate the entire list (affects categories/ordering)
            $this->cacheService->invalidatePermissionsCache();
        } else {
            // Only other attributes changed: invalidate just this permission
            $this->cacheService->invalidatePermission($event->permission->id);
        }
    }

    /**
     * Handle permission deleted event.
     */
    public function handlePermissionDeleted(PermissionDeleted $event): void
    {
        $this->cacheService->invalidatePermissionsCache();
    }

    /**
     * Handle permissions deleted (bulk) event.
     *
     * Uses batch invalidation for better performance when supported
     */
    public function handlePermissionsDeleted(PermissionsDeleted $event): void
    {
        // For bulk deletions, use granular invalidation if IDs are provided
        if (!empty($event->deletedIds)) {
            $this->cacheService->invalidatePermissions($event->deletedIds);
        }

        // Also invalidate the full list since items were removed
        $this->cacheService->invalidatePermissionsCache();
    }
}
