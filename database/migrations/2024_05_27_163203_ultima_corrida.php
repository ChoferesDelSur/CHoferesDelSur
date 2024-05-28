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
        Schema::create('ultimaCorrida', function (Blueprint $table) {
            $table->id(column:'idUltimaCorrida');
            $table->string(column:'ultimaCorrida')->nullable(true);
            $table->time('horaInicioUC')->nullable(true);
            $table->time('horaFinUC')->nullable(true);
            $table->foreignId(column:'idUnidad')->references('idUnidad')->on('unidad');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ultimaCorrida');
    }
};
