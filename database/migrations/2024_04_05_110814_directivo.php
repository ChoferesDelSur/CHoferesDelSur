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
        Schema::create('directivo', function (Blueprint $table) {
            $table->id(column:'idDirectivo');
            $table->string(column: 'nombre')->nullable(false);
            $table->string(column: 'apellidoP')->nullable(false);
            $table->string(column: 'apellidoM')->nullable(false);
            $table->foreignId(column:'idTipoDirectivo')->references('idTipoDirectivo')->on('tipoDirectivo');
            $table->integer('numUnidades')->nullable(true);
            $table->integer('numOperadores')->nullable(true);
            $table->text('nombre_completo')->nullable()->fulltext();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directivo');
    }
};
