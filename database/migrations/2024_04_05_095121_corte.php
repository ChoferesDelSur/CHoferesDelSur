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
            $table->date(column:'horaCorte')->nullable(false);
            $table->string(column:'causa')->nullable(false);
            $table->date(column:'horaRegreso')->nullable(false);
            $table->boolean(column:'notaTalacha')->nullable(false);
            $table->string(column:'tiempoCorte')->nullable(false);
            $table->string(column:'tipoCorte')->nullable(false);
            $table->foreignId(column:'idCastigo')->references('idCastigo')->on('castigo');
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
