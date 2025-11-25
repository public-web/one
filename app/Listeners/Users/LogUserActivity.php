<?php

namespace App\Listeners\Users;

use App\Events\Users\UserCreated;
use App\Events\Users\UserDeleted;
use App\Events\Users\UserForceDeleted;
use App\Events\Users\UserRestored;
use App\Events\Users\UsersImported;
use App\Events\Users\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Log user activity events to the application log
 *
 * This listener handles all user-related events and logs them
 * for auditing and debugging purposes.
 */
class LogUserActivity implements ShouldQueue
{
    /**
     * Handle user created events
     */
    public function handleUserCreated(UserCreated $event): void
    {
        logger()->info('User created', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'user_email' => $event->user->email,
            'created_by' => $event->createdBy?->name,
        ]);
    }

    /**
     * Handle user updated events
     */
    public function handleUserUpdated(UserUpdated $event): void
    {
        logger()->info('User updated', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'changes' => $event->changes,
            'updated_by' => $event->updatedBy?->name,
        ]);
    }

    /**
     * Handle user deleted events
     */
    public function handleUserDeleted(UserDeleted $event): void
    {
        logger()->info('User soft deleted', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'deleted_by' => $event->deletedBy?->name,
        ]);
    }

    /**
     * Handle user restored events
     */
    public function handleUserRestored(UserRestored $event): void
    {
        logger()->info('User restored', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'restored_by' => $event->restoredBy?->name,
        ]);
    }

    /**
     * Handle user force deleted events
     */
    public function handleUserForceDeleted(UserForceDeleted $event): void
    {
        logger()->warning('User permanently deleted', [
            'user_id' => $event->userId,
            'user_name' => $event->userName,
            'user_email' => $event->userEmail,
        ]);
    }

    /**
     * Handle users imported events
     */
    public function handleUsersImported(UsersImported $event): void
    {
        logger()->info('Users imported', [
            'success_count' => $event->successCount,
            'error_count' => $event->errorCount,
            'total_failures' => count($event->failures),
        ]);
    }
}
