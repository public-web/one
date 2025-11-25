<?php

namespace App\Providers;

use App\Events\PasswordChanged;
use App\Events\PermissionCreated;
use App\Events\PermissionDeleted;
use App\Events\PermissionsDeleted;
use App\Events\PermissionUpdated;
use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RolePermissionsUpdated;
use App\Events\RolesDeleted;
use App\Events\RoleUpdated;
use App\Events\Users\UserCreated;
use App\Events\Users\UserDeleted;
use App\Events\Users\UserForceDeleted;
use App\Events\Users\UserPermissionsUpdated;
use App\Events\Users\UserRestored;
use App\Events\Users\UsersImported;
use App\Events\Users\UserUpdated;
use App\Listeners\InvalidatePermissionCache;
use App\Listeners\InvalidateRoleCache;
use App\Listeners\LogPasswordChange;
use App\Listeners\Users\LogPermissionsChange;
use App\Listeners\Users\LogUserActivity;
use App\Listeners\Users\SendUserWelcomeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserCreated::class => [
            SendUserWelcomeNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register LogUserActivity listener with multiple event handlers
        Event::listen(UserCreated::class, [LogUserActivity::class, 'handleUserCreated']);
        Event::listen(UserUpdated::class, [LogUserActivity::class, 'handleUserUpdated']);
        Event::listen(UserDeleted::class, [LogUserActivity::class, 'handleUserDeleted']);
        Event::listen(UserRestored::class, [LogUserActivity::class, 'handleUserRestored']);
        Event::listen(UserForceDeleted::class, [LogUserActivity::class, 'handleUserForceDeleted']);
        Event::listen(UsersImported::class, [LogUserActivity::class, 'handleUsersImported']);

        // Register permissions change listener
        Event::listen(UserPermissionsUpdated::class, LogPermissionsChange::class);

        // Register permission cache invalidation listeners
        Event::listen(PermissionCreated::class, [InvalidatePermissionCache::class, 'handlePermissionCreated']);
        Event::listen(PermissionUpdated::class, [InvalidatePermissionCache::class, 'handlePermissionUpdated']);
        Event::listen(PermissionDeleted::class, [InvalidatePermissionCache::class, 'handlePermissionDeleted']);
        Event::listen(PermissionsDeleted::class, [InvalidatePermissionCache::class, 'handlePermissionsDeleted']);

        // Register role cache invalidation listeners
        Event::listen(RoleCreated::class, [InvalidateRoleCache::class, 'handleRoleCreated']);
        Event::listen(RoleUpdated::class, [InvalidateRoleCache::class, 'handleRoleUpdated']);
        Event::listen(RoleDeleted::class, [InvalidateRoleCache::class, 'handleRoleDeleted']);
        Event::listen(RolePermissionsUpdated::class, [InvalidateRoleCache::class, 'handleRolePermissionsUpdated']);
        Event::listen(RolesDeleted::class, [InvalidateRoleCache::class, 'handleRolesDeleted']);

        // Register password change listener
        Event::listen(PasswordChanged::class, LogPasswordChange::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
