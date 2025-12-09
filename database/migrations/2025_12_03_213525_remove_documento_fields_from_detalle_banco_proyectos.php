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
        Schema::table('detalle_banco_proyectos', function (Blueprint $table) {
            $table->dropColumn(['documento_path', 'documento_nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_banco_proyectos', function (Blueprint $table) {
            $table->string('documento_path')->nullable()->after('observacion')->comment('Ruta del documento adjunto');
            $table->string('documento_nombre')->nullable()->after('documento_path')->comment('Nombre original del documento');
        });
    }
};
