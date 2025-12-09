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
        Schema::table('banco_proyectos', function (Blueprint $table) {
            $table->index('codigo_elemento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banco_proyectos', function (Blueprint $table) {
            $table->dropIndex(['codigo_elemento']);
        });
    }
};
