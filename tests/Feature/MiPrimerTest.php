<?php

namespace Tests\Feature;

use Tests\TestCase;

class MiPrimerTest extends TestCase
{
    public function test_dos_mas_dos_es_cuatro(): void
    {
        // Arrange (Preparar)
        $numero1 = 2;
        $numero2 = 2;
        
        // Act (Actuar)
        $resultado = $numero1 + $numero2;
        
        // Assert (Verificar)
        $this->assertEquals(4, $resultado);
    }

    public function test_la_pagina_de_login_existe(): void
    {
        // Act - Visitar la página de login
        $response = $this->get('/login');
        
        // Assert - Verificar que la página carga exitosamente
        $response->assertStatus(200);
    }
}
