<?php

namespace App\Listeners;

use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RolePermissionsUpdated;
use App\Events\RolesDeleted;
use App\Events\RoleUpdated;
use App\Services\RolePermissionCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidateRoleCache implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private RolePermissionCacheService $cacheService
    ) {}

    /**
     * Handle role created event.
     */
    public function handleRoleCreated(RoleCreated $event): void
    {
        $this->cacheService->invalidateRolesCache();
    }

    /**
     * Handle role updated event.
     */
    public function handleRoleUpdated(RoleUpdated $event): void
    {
        if ($event->nameChanged) {
            // Name changed: invalidate the entire list (affects ordering/filtering)
            $this->cacheService->invalidateRolesCache();
        } else {
            // Only permissions changed: invalidate just this role
            $this->cacheService->invalidateRole($event->role->id);
        }
    }

    /**
     * Handle role deleted event.
     */
    public function handleRoleDeleted(RoleDeleted $event): void
    {
        $this->cacheService->invalidateRolesCache();
    }

    /**
     * Handle role permissions updated event.
     */
    public function handleRolePermissionsUpdated(RolePermissionsUpdated $event): void
    {
        // Only invalidate this specific role's cache (selective invalidation)
        $this->cacheService->invalidateRole($event->role->id);
    }

    /**
     * Handle roles deleted (bulk) event.
     *
     * Uses batch invalidation for better performance when supported
     */
    public function handleRolesDeleted(RolesDeleted $event): void
    {
        // For bulk deletions, use granular invalidation if IDs are provided
        if (!empty($event->deletedIds)) {
            $this->cacheService->invalidateRoles($event->deletedIds);
        }

        // Also invalidate the full list since items were removed
        $this->cacheService->invalidateRolesCache();
    }
}
