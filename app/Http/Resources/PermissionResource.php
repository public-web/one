<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the permission resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => ucfirst(str_replace('.', ' ', $this->name)),
            'guard_name' => $this->guard_name,
            'roles_count' => $this->when(
                isset($this->roles_count),
                $this->roles_count
            ),
            'users_count' => $this->when(
                isset($this->users_count),
                $this->users_count
            ),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
