<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SeguridadRBACTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_sin_permiso_no_puede_acceder(): void
    {
        // Arrange - Crear usuario SIN permisos
        $usuario = User::factory()->create();
        $permiso = Permission::create(['name' => 'eliminar usuarios']);

        // Assert - Verificar que NO tiene el permiso
        $this->assertFalse($usuario->can('eliminar usuarios'));
    }

    public function test_usuario_con_rol_incorrecto_no_tiene_permiso(): void
    {
        // Arrange
        $usuario = User::factory()->create();
        $rolEditor = Role::create(['name' => 'editor']);
        $permisoAdmin = Permission::create(['name' => 'gestionar roles']);
        
        // El usuario es editor (NO admin)
        $usuario->assignRole($rolEditor);

        // Assert - Verificar que NO puede gestionar roles
        $this->assertFalse($usuario->can('gestionar roles'));
    }

    public function test_solo_admin_puede_gestionar_roles(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $editor = User::factory()->create();
        
        $rolAdmin = Role::create(['name' => 'admin']);
        $rolEditor = Role::create(['name' => 'editor']);
        $permiso = Permission::create(['name' => 'gestionar roles']);
        
        $rolAdmin->givePermissionTo($permiso);
        
        $admin->assignRole($rolAdmin);
        $editor->assignRole($rolEditor);

        // Assert
        $this->assertTrue($admin->can('gestionar roles'));
        $this->assertFalse($editor->can('gestionar roles'));
    }
}
