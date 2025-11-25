<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any roles (list roles).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.list');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.list');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.edit');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can sync permissions to the role.
     */
    public function syncPermissions(User $user, Role $role): bool
    {
        return $user->can('roles.edit');
    }

    /**
     * Determine whether the user can delete multiple roles.
     */
    public function deleteMany(User $user): bool
    {
        return $user->can('roles.delete');
    }
}
