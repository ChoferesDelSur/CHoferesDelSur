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
        Schema::create('corte', function (Blueprint $table) {
            $table->id(column:'idCorte');
            $table->time('horaCorte')->nullable(true);
            $table->string(column:'causa')->nullable(true);
            $table->time('horaRegreso')->nullable(true);
            $table->foreignId(column:'idUnidad')->references('idUnidad')->on('unidad');
            $table->foreignId(column:'idRuta')->references('idRuta')->on('ruta');
            $table->foreignId(column:'idDirectivo')->references('idDirectivo')->on('directivo');
            $table->foreignId(column:'idOperador')->references('idOperador')->on('operador');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corte');
    }
};
