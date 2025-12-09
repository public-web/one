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
        Schema::create('detalle_banco_proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->comment('Código del proyecto (relacionado con banco_proyectos)');
            $table->string('numero_radicado_entrada')->nullable()->comment('Número de radicado de entrada');
            $table->date('fecha_entrada')->nullable()->comment('Fecha de entrada');
            $table->string('peticionario')->nullable()->comment('Peticionario');
            $table->text('asunto')->nullable()->comment('Asunto');
            $table->string('numero_radicado_salida')->nullable()->comment('Número de radicado de salida');
            $table->date('fecha_salida')->nullable()->comment('Fecha de salida');
            $table->text('observacion')->nullable()->comment('Observación');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('codigo')
                ->references('codigo_elemento')
                ->on('banco_proyectos')
                ->onDelete('cascade');

            // Index for better performance
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_banco_proyectos');
    }
};
