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
        Schema::create('unidad', function (Blueprint $table) {
            $table->id(column:'idUnidad');
            $table->string(column:'numeroUnidad')->unique()->nullable(false);//nullable(false) quiere decir que no puede estar nulo
            $table->foreignId(column:'idRuta')->references('idRuta')->on('ruta');
            $table->foreignId(column:'idOperador')->nullable(true)->references('idOperador')->on('operador');
            $table->foreignId(column:'idDirectivo')->references('idDirectivo')->on('directivo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad');
    }
};
