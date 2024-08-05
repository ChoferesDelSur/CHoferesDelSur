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
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id(column:'idVacaciones');
            $table->string(column: 'motivo')->nullable(false);
            $table->integer('numeroDias')->nullable(false);
            $table->date('fechaInicio')->nullable(false);
            $table->date('fechaFin')->nullable(false);
            $table->foreignId(column:'idPersonal')->references('idPersonal')->on('personal');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
