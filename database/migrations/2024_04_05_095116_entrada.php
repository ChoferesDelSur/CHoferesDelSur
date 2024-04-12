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
        Schema::create('entrada', function (Blueprint $table) {
            $table->id(column:'idEntrada');
            $table->date(column:'horaEntrada')->nullable(false);
            $table->string(column:'tipoEntrada')->nullable(false);
            $table->boolean(column:'extremo')->nullable(false);
            $table->date(column:'ultimaAyer')->nullable(false);
            $table->integer(column:'multa') -> nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada');
    }
};
