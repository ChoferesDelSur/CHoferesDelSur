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
        Schema::create('operador', function (Blueprint $table) {
            $table->id(column:'idOperador');
            $table->string(column: 'nombre')->nullable(false);
            $table->string(column: 'apellidoP')->nullable(false);
            $table->string(column: 'apellidoM')->nullable(false);
            $table->date(column:'fechaNacimiento')->nullable(false);
            $table->integer('edad')->nullable(false);
            $table->string(column:'CURP')->nullable(false);
            $table->string(column:'RFC')->nullable(false);
            $table->string(column:'numTelefono')->nullable(false);
            $table->string(column:'NSS')->nullable(false);
            $table->foreignId(column: 'idDireccion')->references('idDireccion')->on('direccion');
            $table->foreignId(column:'idTipoOperador')->references('idTipoOperador')->on('tipoOperador');
            $table->string(column:'numLicencia')->nullable(false);
            $table->date(column:'vigenciaLicencia')->nullable(false);
            $table->string(column:'numINE')->nullable(false);
            $table->year(column:'vigenciaINE')->nullable(false);
            $table->date(column:'ultimoContrato')->nullable(false);
            $table->integer('antiguedad')->nullable(true);
            $table->date(column:'fechaAlta')->nullable(false);
            $table->date(column:'fechaBaja')->nullable(true);
            $table->foreignId(column:'idEmpresa')->references('idEmpresa')->on('empresa');
            $table->boolean(column:'constanciaSF');
            $table->foreignId(column:'idConvenioPago')->references('idConvenioPago')->on('convenioPago');
            $table->boolean(column:'cursoSemovi');
            $table->boolean(column:'cursoPsicologico');
            $table->boolean(column:'constanciaSemovi');
            $table->foreignId(column:'idEstado')->references('idEstado')->on('estado');
            $table->foreignId(column:'idDirectivo')->references('idDirectivo')->on('directivo');
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
        Schema::dropIfExists('operador');
    }
};
