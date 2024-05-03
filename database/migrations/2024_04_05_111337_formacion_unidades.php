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
        Schema::create('formacionUnidades', function (Blueprint $table) {
            $table->id(column:'idFormacionUnidades');
            $table->date(column:'fecha')->nullable(true);
            $table->foreignId(column:'idUnidad')->references('idUnidad')->on('unidad');
            $table->time('horaEntrada')->nullable(true);
            $table->string(column:'tipoEntrada')->nullable(true);
            $table->string(column:'extremo')->nullable(true);
            $table->string(column:'ultimaAyer')->nullable(true);
            $table->string(column:'multa')->nullable(true);
            $table->time('horaCorte')->nullable(true);
            $table->string(column:'causa')->nullable(true);
            $table->time('horaRegreso')->nullable(true);
            $table->string(column:'notaTalacha')->nullable(true);
            $table->string(column:'tiempoCorte')->nullable(true);
            $table->string(column:'tipoCorte')->nullable(true);
            $table->foreignId(column:'idCastigo')->nullable()->references('idCastigo')->on('castigo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formacionUnidades');
    }
};
