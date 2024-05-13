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
            $table->foreignId(column:'idUnidad')->references('idUnidad')->on('unidad');
            $table->string(column:'trabajaDomingo')->nullable(true);
            $table->time('horaEntrada')->nullable(true);
            $table->string(column:'tipoEntrada')->nullable(true);
            $table->string(column:'extremo')->nullable(true);
            $table->time('horaCorte')->nullable(true);
            $table->string(column:'causa')->nullable(true);
            $table->time('horaRegreso')->nullable(true);
            $table->string(column:'ultimaCorrida')->nullable(true);
            $table->time('horaInicioUC')->nullable(true);
            $table->time('horaFinUC')->nullable(true);
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
