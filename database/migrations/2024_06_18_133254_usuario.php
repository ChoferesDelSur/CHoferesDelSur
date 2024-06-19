<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('idUsuario');
            $table->string(column: 'nombre')->nullable(false);
            $table->string(column: 'apellidoP')->nullable(false);
            $table->string(column: 'apellidoM')->nullable(false);
            $table->string('usuario')->unique()->nullable(false);
            $table->string('password')->nullable(false);
            $table->string('contrasenia')->nullable(false);
            $table->integer('intentos')->nullable(false)->default(10);
            $table->dateTime('fecha_Creacion')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('cambioContrasenia')->nullable(false)->default(false);
            $table->foreignId(column:'idTipoUsuario')->references('idTipoUsuario')->on('tipoUsuario');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
