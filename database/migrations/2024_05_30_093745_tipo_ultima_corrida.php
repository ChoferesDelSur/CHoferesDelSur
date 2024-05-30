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
        Schema::create('tipoUltimaCorrida', function (Blueprint $table) {
            $table->id(column:'idTipoUltimaCorrida');
            $table->string(column: 'tipoUltimaCorrida')->unique()->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipoUltimaCorrida');
    }
};
