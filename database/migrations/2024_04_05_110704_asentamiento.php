<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\municipio;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asentamiento', function (Blueprint $table) {
            $table->id(column:'idAsentamiento');
            $table->string(column:'asentamiento');
            $table->string(column:'tipo');
            $table->foreignId(column:'idMunicipio')->references('idMunicipio')->on('municipio');
            $table->foreignId(column:'idCodigoPostal')->references('idCodigoPostal')->on('codigoPostal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asentamiento');
    }
};
