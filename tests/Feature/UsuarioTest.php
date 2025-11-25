<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase; // Esto limpia la BD después de cada test

    public function test_puede_crear_un_usuario(): void
    {
        // Arrange - Crear un usuario
        $usuario = User::create([
            'name' => 'Juan Carlos',
            'email' => 'juan@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Assert - Verificar que se guardó en la BD
        $this->assertDatabaseHas('users', [
            'email' => 'juan@test.com',
        ]);
    }

    public function test_usuario_puede_iniciar_sesion(): void
    {
        // Arrange - Crear un usuario
        $usuario = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        // Act - Intentar hacer login
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Assert - Verificar que fue redirigido (login exitoso)
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($usuario);
    }
}
