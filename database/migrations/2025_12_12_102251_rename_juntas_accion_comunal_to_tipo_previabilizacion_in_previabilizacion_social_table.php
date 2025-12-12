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
        Schema::table('previabilizacion_social', function (Blueprint $table) {
            $table->renameColumn('juntas_accion_comunal', 'tipo_previabilizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('previabilizacion_social', function (Blueprint $table) {
            $table->renameColumn('tipo_previabilizacion', 'juntas_accion_comunal');
        });
    }
};
