<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerRealTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Permission::create(['name' => 'users.list']);
        Permission::create(['name' => 'users.view']);
        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.edit']);
        Permission::create(['name' => 'users.delete']);
        Permission::create(['name' => 'users.restore']);
        Permission::create(['name' => 'users.force-delete']);
        
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'users.list',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.restore',
            'users.force-delete',
        ]);
        
        Role::create(['name' => 'user']);
    }

    public function test_admin_puede_ver_lista_de_usuarios(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        User::factory()->count(3)->create();
        $response = $this->actingAs($admin)->get('/users');
        $response->assertOk();
    }

    public function test_usuario_sin_permisos_no_puede_ver_usuarios(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($user)->get('/users');
        $response->assertForbidden();
    }

    public function test_admin_puede_crear_usuario(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'active' => true,
        ]);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
        ]);
    }

    public function test_admin_puede_actualizar_usuario(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create([
            'name' => 'Usuario Original',
            'email' => 'original@test.com',
        ]);
        $user->assignRole('user');
        $response = $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => 'Usuario Actualizado',
            'email' => 'original@test.com',
            'role' => 'user',
            'active' => true,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Usuario actualizado exitosamente');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario Actualizado',
        ]);
    }

    public function test_admin_puede_eliminar_usuario_soft_delete(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('user');
        $response = $this->actingAs($admin)->delete("/users/{$user->id}");
        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_puede_restaurar_usuario_eliminado(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->delete();
        $response = $this->actingAs($admin)->post("/users/{$user->id}/restore");
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    public function test_admin_puede_eliminar_permanentemente(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->delete();
        $response = $this->actingAs($admin)->delete("/users/{$user->id}/force");
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_puede_buscar_usuarios_por_nombre(): void
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('admin');
        User::factory()->create(['name' => 'Juan Carlos']);
        User::factory()->create(['name' => 'María López']);
        $response = $this->actingAs($admin)->get('/users?search=Juan');
        $response->assertOk();
    }

    public function test_puede_filtrar_usuarios_por_rol(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user1 = User::factory()->create();
        $user1->assignRole('user');
        $admin2 = User::factory()->create();
        $admin2->assignRole('admin');
        $response = $this->actingAs($admin)->get('/users?role=user');
        $response->assertOk();
    }

    public function test_admin_puede_eliminarse_a_si_mismo(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");
        $this->assertSoftDeleted('users', ['id' => $admin->id]);
    }
}
