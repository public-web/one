<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Service for managing cached roles and permissions with granular cache support
 *
 * Provides centralized caching for frequently accessed roles and permissions
 * with automatic cache invalidation support. Uses cache tags for efficient
 * selective invalidation (requires Redis or Memcached).
 *
 * Cache Strategy:
 * - Tags-based caching for efficient selective invalidation
 * - Individual item caching by ID
 * - Category-based caching for permissions (users.*, roles.*, etc.)
 * - Batch invalidation support
 */
class RolePermissionCacheService
{
    /**
     * Cache duration in seconds (60 minutes)
     */
    private const CACHE_TTL = 3600;

    /**
     * Cache tags for roles and permissions
     */
    private const ROLES_TAG = 'roles';
    private const PERMISSIONS_TAG = 'permissions';

    /**
     * Cache key for roles list
     */
    private const ROLES_CACHE_KEY = 'roles_list';

    /**
     * Cache key for permissions list
     */
    private const PERMISSIONS_CACHE_KEY = 'permissions_list';

    /**
     * Cache key prefix for individual roles
     */
    private const ROLE_CACHE_PREFIX = 'role_';

    /**
     * Cache key prefix for individual permissions
     */
    private const PERMISSION_CACHE_PREFIX = 'permission_';

    /**
     * Cache key prefix for permission categories
     */
    private const PERMISSION_CATEGORY_PREFIX = 'permissions_category_';

    /**
     * Check if cache tags are supported by the current cache driver
     *
     * @return bool
     */
    private function supportsTags(): bool
    {
        return in_array(config('cache.default'), ['redis', 'memcached']);
    }

    /**
     * Get all roles from cache with tag support
     *
     * @param array $columns Columns to select
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles(array $columns = ['id', 'name']): \Illuminate\Database\Eloquent\Collection
    {
        if ($this->supportsTags()) {
            return Cache::tags([self::ROLES_TAG])->remember(
                self::ROLES_CACHE_KEY,
                self::CACHE_TTL,
                fn() => Role::all($columns)
            );
        }

        return Cache::remember(
            self::ROLES_CACHE_KEY,
            self::CACHE_TTL,
            fn() => Role::all($columns)
        );
    }

    /**
     * Get a specific role from cache with tag support
     *
     * @param int $roleId
     * @return \Spatie\Permission\Models\Role|null
     */
    public function getRole(int $roleId): ?Role
    {
        $cacheKey = self::ROLE_CACHE_PREFIX . $roleId;

        if ($this->supportsTags()) {
            return Cache::tags([self::ROLES_TAG])->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => Role::with('permissions')->find($roleId)
            );
        }

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Role::with('permissions')->find($roleId)
        );
    }

    /**
     * Get all permissions from cache with tag support
     *
     * @param array $columns Columns to select
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection
    {
        if ($this->supportsTags()) {
            return Cache::tags([self::PERMISSIONS_TAG])->remember(
                self::PERMISSIONS_CACHE_KEY,
                self::CACHE_TTL,
                fn() => Permission::all($columns)
            );
        }

        return Cache::remember(
            self::PERMISSIONS_CACHE_KEY,
            self::CACHE_TTL,
            fn() => Permission::all($columns)
        );
    }

    /**
     * Get permissions by category from cache with tag support
     *
     * @param string $category Permission category (e.g., 'users', 'roles')
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionsByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = self::PERMISSION_CATEGORY_PREFIX . $category;

        if ($this->supportsTags()) {
            return Cache::tags([self::PERMISSIONS_TAG, "category:{$category}"])->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => Permission::where('name', 'like', "{$category}.%")->get()
            );
        }

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Permission::where('name', 'like', "{$category}.%")->get()
        );
    }

    /**
     * Get a specific permission from cache with tag support
     *
     * @param int $permissionId
     * @return \Spatie\Permission\Models\Permission|null
     */
    public function getPermission(int $permissionId): ?Permission
    {
        $cacheKey = self::PERMISSION_CACHE_PREFIX . $permissionId;

        if ($this->supportsTags()) {
            return Cache::tags([self::PERMISSIONS_TAG])->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn() => Permission::with('roles')->find($permissionId)
            );
        }

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Permission::with('roles')->find($permissionId)
        );
    }

    /**
     * Invalidate a specific role's cache
     *
     * Use this when updating only one role (e.g., changing permissions)
     * This is more efficient than invalidating all roles
     *
     * @param int $roleId
     */
    public function invalidateRole(int $roleId): void
    {
        $cacheKey = self::ROLE_CACHE_PREFIX . $roleId;

        if ($this->supportsTags()) {
            Cache::tags([self::ROLES_TAG])->forget($cacheKey);
        } else {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Invalidate multiple specific roles' cache
     *
     * Use this for batch role updates
     *
     * @param array $roleIds
     */
    public function invalidateRoles(array $roleIds): void
    {
        foreach ($roleIds as $roleId) {
            $this->invalidateRole($roleId);
        }
    }

    /**
     * Invalidate a specific permission's cache
     *
     * Use this when updating only one permission
     *
     * @param int $permissionId
     */
    public function invalidatePermission(int $permissionId): void
    {
        $cacheKey = self::PERMISSION_CACHE_PREFIX . $permissionId;

        if ($this->supportsTags()) {
            Cache::tags([self::PERMISSIONS_TAG])->forget($cacheKey);
        } else {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Invalidate multiple specific permissions' cache
     *
     * Use this for batch permission updates
     *
     * @param array $permissionIds
     */
    public function invalidatePermissions(array $permissionIds): void
    {
        foreach ($permissionIds as $permissionId) {
            $this->invalidatePermission($permissionId);
        }
    }

    /**
     * Invalidate permissions cache for a specific category
     *
     * Efficient way to invalidate only permissions in a category (e.g., 'users.*')
     *
     * @param string $category Permission category (e.g., 'users', 'roles')
     */
    public function invalidatePermissionCategory(string $category): void
    {
        $cacheKey = self::PERMISSION_CATEGORY_PREFIX . $category;

        if ($this->supportsTags()) {
            // Flush all cache with this category tag
            Cache::tags(["category:{$category}"])->flush();
        } else {
            // Fallback to forgetting the specific category key
            Cache::forget($cacheKey);
        }
    }

    /**
     * Invalidate roles cache
     *
     * Call this when roles are created or deleted
     * For updates to a single role's permissions, use invalidateRole() instead
     */
    public function invalidateRolesCache(): void
    {
        if ($this->supportsTags()) {
            // Flush all cache tagged with 'roles'
            Cache::tags([self::ROLES_TAG])->flush();
        } else {
            // Fallback: forget only the list key
            Cache::forget(self::ROLES_CACHE_KEY);
        }
    }

    /**
     * Invalidate permissions cache
     *
     * Call this when permissions are created or deleted
     * For updates to a single permission, use invalidatePermission() instead
     */
    public function invalidatePermissionsCache(): void
    {
        if ($this->supportsTags()) {
            // Flush all cache tagged with 'permissions'
            Cache::tags([self::PERMISSIONS_TAG])->flush();
        } else {
            // Fallback: forget only the list key
            Cache::forget(self::PERMISSIONS_CACHE_KEY);
        }
    }

    /**
     * Invalidate all caches
     *
     * Useful when making bulk changes
     */
    public function invalidateAll(): void
    {
        $this->invalidateRolesCache();
        $this->invalidatePermissionsCache();
    }

    /**
     * Refresh roles cache
     *
     * Forces a fresh fetch and caches the result
     */
    public function refreshRolesCache(): \Illuminate\Database\Eloquent\Collection
    {
        $this->invalidateRolesCache();
        return $this->getRoles();
    }

    /**
     * Refresh permissions cache
     *
     * Forces a fresh fetch and caches the result
     */
    public function refreshPermissionsCache(): \Illuminate\Database\Eloquent\Collection
    {
        $this->invalidatePermissionsCache();
        return $this->getPermissions();
    }

    /**
     * Cache a specific role with tag support
     *
     * Useful after updating a role to immediately update its cache
     *
     * @param \Spatie\Permission\Models\Role $role
     * @return \Spatie\Permission\Models\Role
     */
    public function cacheRole(Role $role): Role
    {
        // Load permissions if not already loaded
        if (!$role->relationLoaded('permissions')) {
            $role->load('permissions');
        }

        $cacheKey = self::ROLE_CACHE_PREFIX . $role->id;

        if ($this->supportsTags()) {
            Cache::tags([self::ROLES_TAG])->put($cacheKey, $role, self::CACHE_TTL);
        } else {
            Cache::put($cacheKey, $role, self::CACHE_TTL);
        }

        return $role;
    }

    /**
     * Cache a specific permission with tag support
     *
     * Useful after updating a permission to immediately update its cache
     *
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Spatie\Permission\Models\Permission
     */
    public function cachePermission(Permission $permission): Permission
    {
        // Load roles if not already loaded
        if (!$permission->relationLoaded('roles')) {
            $permission->load('roles');
        }

        $cacheKey = self::PERMISSION_CACHE_PREFIX . $permission->id;
        $category = explode('.', $permission->name)[0] ?? null;

        if ($this->supportsTags()) {
            $tags = [self::PERMISSIONS_TAG];
            if ($category) {
                $tags[] = "category:{$category}";
            }
            Cache::tags($tags)->put($cacheKey, $permission, self::CACHE_TTL);
        } else {
            Cache::put($cacheKey, $permission, self::CACHE_TTL);
        }

        return $permission;
    }

    /**
     * Refresh a specific role's cache
     *
     * @param int $roleId
     * @return \Spatie\Permission\Models\Role|null
     */
    public function refreshRole(int $roleId): ?Role
    {
        $this->invalidateRole($roleId);
        return $this->getRole($roleId);
    }

    /**
     * Refresh a specific permission's cache
     *
     * @param int $permissionId
     * @return \Spatie\Permission\Models\Permission|null
     */
    public function refreshPermission(int $permissionId): ?Permission
    {
        $this->invalidatePermission($permissionId);
        return $this->getPermission($permissionId);
    }
}

