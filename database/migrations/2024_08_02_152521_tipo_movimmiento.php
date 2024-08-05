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
        Schema::create('tipoMovimiento', function (Blueprint $table) {
            $table->id(column:'idTipoMovimiento');
            $table->string(column: 'tipoMovimiento')->unique()->nullable(false);
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
        Schema::dropIfExists('tipoMovimiento');
    }
};
