<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('previabilizacion_social', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->comment('Código del proyecto');
            $table->date('fecha')->nullable()->comment('Fecha de previabilización');
            $table->string('priorizado_por')->nullable()->comment('Priorizado por');
            $table->string('juntas_accion_comunal')->nullable()->comment('Juntas de Acción Comunal');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('codigo')
                ->references('codigo_elemento')
                ->on('banco_proyectos')
                ->onDelete('cascade');

            // Index for faster queries
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previabilizacion_social');
    }
};
