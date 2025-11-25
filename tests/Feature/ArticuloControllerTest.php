<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticuloControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_crear_articulo(): void
    {
        // Arrange
        $usuario = User::factory()->create();

        // Act - Hacer POST para crear artículo
        $response = $this->actingAs($usuario)->post('/articulos', [
            'titulo' => 'Mi primer artículo',
            'contenido' => 'Este es el contenido del artículo',
        ]);

        // Assert
        $this->assertDatabaseHas('articulos', [
            'titulo' => 'Mi primer artículo',
            'user_id' => $usuario->id,
        ]);
    }

    public function test_invitado_no_puede_crear_articulo(): void
    {
        // Act - Intentar crear sin estar autenticado
        $response = $this->post('/articulos', [
            'titulo' => 'Artículo de invitado',
            'contenido' => 'Contenido',
        ]);

        // Assert - Debe redirigir a login
        $response->assertRedirect('/login');
        
        // Verificar que NO se creó en la BD
        $this->assertDatabaseMissing('articulos', [
            'titulo' => 'Artículo de invitado',
        ]);
    }

    public function test_puede_ver_lista_de_articulos(): void
    {
        // Arrange - Crear 3 artículos
        $usuario = User::factory()->create();
        Articulo::factory()->count(3)->create(['user_id' => $usuario->id]);

        // Act
        $response = $this->actingAs($usuario)->get('/articulos');

        // Assert
        $response->assertStatus(200);
    }

    public function test_puede_actualizar_su_propio_articulo(): void
    {
        // Arrange
        $usuario = User::factory()->create();
        $articulo = Articulo::factory()->create([
            'user_id' => $usuario->id,
            'titulo' => 'Título original',
        ]);

        // Act - Actualizar
        $response = $this->actingAs($usuario)->put("/articulos/{$articulo->id}", [
            'titulo' => 'Título actualizado',
            'contenido' => 'Contenido actualizado',
        ]);

        // Assert
        $this->assertDatabaseHas('articulos', [
            'id' => $articulo->id,
            'titulo' => 'Título actualizado',
        ]);
    }

    public function test_no_puede_actualizar_articulo_de_otro_usuario(): void
    {
        // Arrange - Dos usuarios diferentes
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        
        $articulo = Articulo::factory()->create([
            'user_id' => $usuario1->id,
            'titulo' => 'Artículo de usuario 1',
        ]);

        // Act - Usuario 2 intenta actualizar artículo de Usuario 1
        $response = $this->actingAs($usuario2)->put("/articulos/{$articulo->id}", [
            'titulo' => 'Intento de modificación',
            'contenido' => 'Hacker intentando modificar',
        ]);

        // Assert - Debe ser prohibido
        $response->assertForbidden();
        
        // El título NO debe cambiar
        $this->assertDatabaseHas('articulos', [
            'id' => $articulo->id,
            'titulo' => 'Artículo de usuario 1',
        ]);
    }
}
