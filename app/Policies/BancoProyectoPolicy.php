<?php

namespace App\Policies;

use App\Models\BancoProyecto;
use App\Models\User;

class BancoProyectoPolicy
{
    /**
     * Perform pre-authorization checks.
     * Superadmin bypasses all authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Check if user has banco-proyectos role
     */
    private function hasBancoProyectosRole(User $user): bool
    {
        return $user->hasRole('banco-proyectos');
    }

    /**
     * Check if user can view banco proyectos (read-only access)
     */
    private function canViewBancoProyectos(User $user): bool
    {
        return $user->hasAnyRole(['banco-proyectos', 'previabilizacion-social']);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->canViewBancoProyectos($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BancoProyecto $bancoProyecto): bool
    {
        return $this->canViewBancoProyectos($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasBancoProyectosRole($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BancoProyecto $bancoProyecto): bool
    {
        return $this->hasBancoProyectosRole($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BancoProyecto $bancoProyecto): bool
    {
        return $this->hasBancoProyectosRole($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BancoProyecto $bancoProyecto): bool
    {
        return $this->hasBancoProyectosRole($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BancoProyecto $bancoProyecto): bool
    {
        return $this->hasBancoProyectosRole($user);
    }
}
