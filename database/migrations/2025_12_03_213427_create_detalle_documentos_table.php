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
        Schema::create('detalle_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detalle_banco_proyecto_id')->constrained('detalle_banco_proyectos')->onDelete('cascade');
            $table->string('archivo_path')->comment('Ruta del archivo en storage');
            $table->string('archivo_nombre')->comment('Nombre original del archivo');
            $table->string('archivo_tipo')->nullable()->comment('Tipo MIME del archivo');
            $table->integer('archivo_tamanio')->nullable()->comment('Tamaño del archivo en bytes');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('detalle_banco_proyecto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_documentos');
    }
};
