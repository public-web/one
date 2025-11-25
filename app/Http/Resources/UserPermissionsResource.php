<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for detailed user permissions view
 *
 * Returns three types of permissions:
 * - all_permissions: All available permissions in the system
 * - role_permissions: Inherited from assigned roles (read-only)
 * - direct_permissions: Explicitly assigned to this user (editable)
 */
class UserPermissionsResource extends JsonResource
{
    /**
     * Transform the user permissions resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use cached permissions for better performance
        $cacheService = app(\App\Services\RolePermissionCacheService::class);

        return [
            'all_permissions' => PermissionResource::collection(
                $cacheService->getPermissions()
            ),
            'role_permissions' => $this->getPermissionsViaRoles()->pluck('name'),
            'direct_permissions' => $this->permissions->pluck('name'),
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'roles' => $this->roles->pluck('name'),
            ],
        ];
    }
}
