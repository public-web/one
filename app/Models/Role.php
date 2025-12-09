<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * System roles that cannot be deleted or renamed
     */
    public const SYSTEM_ROLES = ['superadmin', 'user'];

    /**
     * Check if this role is a system role
     *
     * @return bool
     */
    public function isSystemRole(): bool
    {
        return in_array($this->name, self::SYSTEM_ROLES);
    }

    /**
     * Check if this role can be deleted
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        return !$this->isSystemRole() && !$this->users()->exists();
    }

    /**
     * Scope: Filter only non-system roles
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonSystem($query)
    {
        return $query->whereNotIn('name', self::SYSTEM_ROLES);
    }

    /**
     * Scope: Filter only system roles
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystemOnly($query)
    {
        return $query->whereIn('name', self::SYSTEM_ROLES);
    }
}
