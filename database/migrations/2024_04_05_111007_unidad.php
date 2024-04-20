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
            $table->string(column:'numeroUnidad')->unique()->nullable(false);
            $table->string(column:'nombreUnidad')->nullable(false);
            $table->foreignId(column:'idRuta')->references('idRuta')->on('ruta');
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
        Schema::dropIfExists('unidad');
    }
};
