<?php

namespace App\Services;

use App\Events\Users\UserCreated;
use App\Events\Users\UserDeleted;
use App\Events\Users\UserForceDeleted;
use App\Events\Users\UserRestored;
use App\Events\Users\UserUpdated;
use App\Models\User;
use App\Notifications\NewUserCreated;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        private TwoFactorService $twoFactorService
    ) {}

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Generate temporary password
            $temporaryPassword = config('app.default_password');

            // Create user
            $user = User::create([
                'name' => ucwords(strtolower(trim($data['name']))),
                'email' => $data['email'],
                'password' => $temporaryPassword, // Auto-hashed by 'hashed' cast
                'password_changed_at' => null, // Force password change on first login
                'active' => $data['active'] ?? true,
                'expires_at' => $data['expires_at'] ?? null,
                'require_2fa' => $data['require_2fa'] ?? false,
                'avatar' => isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile
                    ? $data['avatar']->store('avatars', 'public')
                    : null,
            ]);

            // Assign role
            $user->assignRole($data['role']);

            // Handle 2FA if required
            if ($data['require_2fa'] ?? false) {
                $this->twoFactorService->enable($user);
            }

            // Send welcome email with temporary password (queued with delay)
            $user->notify(
                (new NewUserCreated($temporaryPassword))
                    ->delay(now()->addSeconds(5))
            );

            // Dispatch user created event
            UserCreated::dispatch($user, auth()->user());

            return $user->fresh(['roles']);
        });
    }

    /**
     * Update an existing user
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Capture changes for event
            $changes = [];
            foreach (['name', 'email', 'active', 'expires_at', 'require_2fa'] as $field) {
                if (isset($data[$field]) && $user->{$field} != $data[$field]) {
                    $changes[$field] = [
                        'old' => $user->{$field},
                        'new' => $data[$field],
                    ];
                }
            }

            // Update user data
            $user->update([
                'name' => ucwords(strtolower(trim($data['name']))),
                'email' => $data['email'],
                'active' => $data['active'] ?? true,
                'expires_at' => $data['expires_at'] ?? null,
                'require_2fa' => $data['require_2fa'] ?? false,
            ]);

            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                if ($user->avatar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                $user->update(['avatar' => $data['avatar']->store('avatars', 'public')]);
                $changes['avatar'] = ['old' => 'previous_image', 'new' => 'new_image'];
            }

            // Handle 2FA changes
            $this->twoFactorService->toggle($user, $data['require_2fa'] ?? false);

            // Update role
            $oldRole = $user->roles->first()?->name;
            $user->syncRoles([$data['role']]);
            if ($oldRole !== $data['role']) {
                $changes['role'] = ['old' => $oldRole, 'new' => $data['role']];
            }

            // Dispatch user updated event with changes
            if (!empty($changes)) {
                UserUpdated::dispatch($user->fresh(), $changes, auth()->user());
            }

            return $user->fresh(['roles']);
        });
    }

    /**
     * Soft delete a user
     */
    public function deleteUser(User $user): bool
    {
        try {
            $result = $user->delete();

            if ($result) {
                // Dispatch user deleted event
                UserDeleted::dispatch($user, auth()->user());
            }

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Error al eliminar usuario (soft delete)', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('No se puede eliminar el usuario porque tiene datos relacionados. Contacte al administrador.');
        }
    }

    /**
     * Restore a soft-deleted user
     */
    public function restoreUser(int $id): User
    {
        $user = User::withTrashed()->findOrFail($id);

        try {
            $user->restore();

            // Dispatch user restored event
            UserRestored::dispatch($user->fresh(), auth()->user());

            return $user->fresh(['roles']);
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Error al restaurar usuario', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('No se puede restaurar el usuario debido a conflictos con la base de datos.');
        }
    }

    /**
     * Permanently delete a user
     */
    public function forceDeleteUser(int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);

        // Capture user data before deletion for event
        $userId = $user->id;
        $userName = $user->name;
        $userEmail = $user->email;

        try {
            $result = $user->forceDelete();

            if ($result) {
                // Dispatch user force deleted event (user no longer exists)
                UserForceDeleted::dispatch($userId, $userName, $userEmail);
            }

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Error al eliminar usuario permanentemente', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);

            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint')) {
                throw new \Exception('No se puede eliminar permanentemente el usuario porque tiene registros relacionados (logs, permisos, etc.). Elimine primero las dependencias o contacte al administrador.');
            }

            throw new \Exception('Error de base de datos al eliminar el usuario permanentemente.');
        }
    }

    /**
     * Sync direct permissions for a user
     */
    public function syncDirectPermissions(User $user, array $permissions): User
    {
        // Capture old permissions before sync
        $oldPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        // Sync permissions
        $user->syncPermissions($permissions);

        // Dispatch event for auditing
        \App\Events\Users\UserPermissionsUpdated::dispatch(
            $user,
            $oldPermissions,
            $permissions,
            auth()->user()
        );

        return $user->fresh(['roles', 'permissions']);
    }
}
