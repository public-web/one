<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesYPermisosTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_un_rol(): void
    {
        // Act - Crear un rol
        $rol = Role::create(['name' => 'admin']);

        // Assert - Verificar que existe
        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);
    }

    public function test_puede_asignar_rol_a_usuario(): void
    {
        // Arrange - Crear usuario y rol
        $usuario = User::factory()->create();
        $rol = Role::create(['name' => 'editor']);

        // Act - Asignar rol
        $usuario->assignRole($rol);

        // Assert - Verificar que tiene el rol
        $this->assertTrue($usuario->hasRole('editor'));
    }

    public function test_usuario_con_permiso_puede_acceder(): void
    {
        // Arrange - Crear usuario, rol y permiso
        $usuario = User::factory()->create();
        $rol = Role::create(['name' => 'moderador']);
        $permiso = Permission::create(['name' => 'editar articulos']);
        
        $rol->givePermissionTo($permiso);
        $usuario->assignRole($rol);

        // Assert - Verificar que tiene el permiso
        $this->assertTrue($usuario->can('editar articulos'));
    }
}
