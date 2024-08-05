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
        Schema::create('movimiento', function (Blueprint $table) {
            $table->id(column:'idMovimiento');
            $table->date('fechaMovimiento')->nullable(false);
            $table->foreignId(column:'idTipoMovimiento')->references('idTipoMovimiento')->on('tipoMovimiento');
            $table->foreignId(column:'idDirectivo')->references('idDirectivo')->on('directivo');
            $table->foreignId(column:'idOperador')->references('idOperador')->on('operador');
            $table->foreignId(column:'idEstado')->references('idEstado')->on('estado');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento');
    }
};
