<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any permissions (list permissions).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('permissions.list');
    }

    /**
     * Determine whether the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->can('permissions.list');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user): bool
    {
        return $user->can('permissions.create');
    }

    /**
     * Determine whether the user can update the permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->can('permissions.edit');
    }

    /**
     * Determine whether the user can delete the permission.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->can('permissions.delete');
    }

    /**
     * Determine whether the user can delete multiple permissions.
     */
    public function deleteMany(User $user): bool
    {
        return $user->can('permissions.delete');
    }
}
