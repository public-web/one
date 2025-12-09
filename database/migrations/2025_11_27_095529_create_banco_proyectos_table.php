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
        Schema::create('banco_proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_elemento_civ_rupi')->nullable()->comment('Tipo de elemento CIV/RUPI');
            $table->string('codigo_elemento')->comment('Código del elemento');
            $table->string('uso')->nullable()->comment('Uso del elemento');
            $table->string('area_elemento')->nullable()->comment('Área del elemento');
            $table->string('localidad')->nullable()->comment('Localidad');
            $table->string('upl')->nullable()->comment('UPL');
            $table->string('barrio')->nullable()->comment('Barrio');
            $table->text('tramo_direccion')->nullable()->comment('Tramo/Dirección');
            $table->string('eje')->nullable()->comment('Eje');
            $table->string('inicio')->nullable()->comment('Inicio');
            $table->string('fin')->nullable()->comment('Fin');
            $table->string('reserva')->nullable()->comment('Reserva');
            $table->string('estado')->nullable()->comment('Estado');
            $table->string('id_contrato')->nullable()->comment('ID del contrato');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banco_proyectos');
    }
};
