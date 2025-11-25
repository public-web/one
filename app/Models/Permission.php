<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * Get all unique permission categories
     *
     * Extracts the prefix from permission names (e.g., "users" from "users.create")
     * Useful for filtering and grouping permissions by their domain.
     *
     * @return Collection
     */
    public static function categories(): Collection
    {
        return static::pluck('name')
            ->map(fn($name) => explode('.', $name)[0])
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get permissions grouped by category
     *
     * Returns permissions organized by their category prefix.
     * Example: ['users' => [...], 'roles' => [...]]
     *
     * @return Collection
     */
    public static function groupedByCategory(): Collection
    {
        return static::all()
            ->groupBy(fn($permission) => explode('.', $permission->name)[0])
            ->sortKeys();
    }

    /**
     * Scope: Filter permissions by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('name', 'like', "{$category}.%");
    }
}
