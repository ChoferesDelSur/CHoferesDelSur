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
            $table->date(column:'fecha')->nullable(false);
            $table->foreignId(column:'idUnidad')->references('idUnidad')->on('unidad');
            $table->foreignId(column:'idEntrada')->references('idEntrada')->on('entrada');
            $table->foreignId(column:'idCorte')->references('idCorte')->on('corte');
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
