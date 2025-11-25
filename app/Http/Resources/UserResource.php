<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'active' => $this->active,
            'require_2fa' => $this->require_2fa,
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'expires_at_human' => $this->expires_at?->diffForHumans(),
            'has_expired' => $this->hasExpired(),
            'is_active_and_not_expired' => $this->isActiveAndNotExpired(),
            'needs_password_change' => $this->needsPasswordChange(),
            'has_two_factor_enabled' => $this->hasEnabledTwoFactorAuthentication(),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'password_changed_at' => $this->password_changed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                ]);
            }),

            'primary_role' => $this->whenLoaded('roles', fn() => $this->getPrimaryRole()),

            'permissions' => $this->when(
                $request->user()?->can('view', $this->resource),
                function () {
                    return [
                        'role_permissions' => $this->getPermissionsViaRoles()->pluck('name'),
                        'direct_permissions' => $this->permissions->pluck('name'),
                        'all_permissions' => $this->getAllPermissions()->pluck('name'),
                    ];
                }
            ),
        ];
    }
}
