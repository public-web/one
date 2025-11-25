<?php

namespace App\Listeners\Users;

use App\Events\Users\UserPermissionsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * LogPermissionsChange Listener
 *
 * Logs permission changes to activity log for audit trail
 * Runs asynchronously in queue for better performance
 */
class LogPermissionsChange implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserPermissionsUpdated $event): void
    {
        // Only log if there were actual changes
        if (!$event->hasChanges()) {
            return;
        }

        $added = $event->getAddedPermissions();
        $removed = $event->getRemovedPermissions();

        // Build human-readable description
        $description = 'Permisos directos actualizados';

        if (count($added) > 0 && count($removed) > 0) {
            $description = sprintf(
                'Agregados: %s | Removidos: %s',
                implode(', ', $added),
                implode(', ', $removed)
            );
        } elseif (count($added) > 0) {
            $description = sprintf('Permisos agregados: %s', implode(', ', $added));
        } elseif (count($removed) > 0) {
            $description = sprintf('Permisos removidos: %s', implode(', ', $removed));
        }

        // Log activity using Spatie Activity Log
        activity()
            ->performedOn($event->user)
            ->causedBy($event->updatedBy)
            ->withProperties([
                'old_permissions' => $event->oldPermissions,
                'new_permissions' => $event->newPermissions,
                'added' => $added,
                'removed' => $removed,
                'auth_user_id' => $event->updatedBy?->id,
            ])
            ->event('permissions_updated')
            ->log($description);

        // Also log to Laravel log for debugging
        logger()->info('User permissions updated', [
            'user_id' => $event->user->id,
            'auth_user_id' => $event->updatedBy?->id,
            'added_permissions' => $added,
            'removed_permissions' => $removed,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserPermissionsUpdated $event, \Throwable $exception): void
    {
        logger()->error('Failed to log permissions change', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
