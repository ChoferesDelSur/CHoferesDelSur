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
        Schema::create('personal', function (Blueprint $table) {
            $table->id(column:'idPersonal');
            $table->string(column: 'nombre')->nullable(false);
            $table->string(column: 'apellidoP')->nullable(false);
            $table->string(column: 'apellidoM')->nullable(false);
            $table->date(column:'fechaNacimiento')->nullable(false);
            $table->integer('edad')->nullable(false);
            $table->string(column:'CURP')->nullable(false);
            $table->string(column:'RFC')->nullable(false);
            $table->string(column:'numTelefono')->nullable(false);
            $table->string(column:'telEmergencia')->nullable(true);
            $table->string(column:'NSS')->nullable(false);
            $table->foreignId(column: 'idDireccion')->references('idDireccion')->on('direccion');
            $table->string(column:'numINE')->nullable(true);
            $table->year(column:'vigenciaINE')->nullable(true);
            $table->integer('antiguedad')->nullable(true);
            $table->date(column:'fechaAlta')->nullable(false);
            $table->date(column:'fechaBaja')->nullable(true);
            $table->foreignId(column:'idEmpresa')->references('idEmpresa')->on('empresa');
            $table->foreignId(column:'idEscolaridad')->references('idEscolaridad')->on('escolaridad');
            $table->boolean(column:'constanciaSF');
            $table->integer('totalDiasVac')->nullable(true);
            $table->integer('diasVacRestantes')->nullable(true);
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
        Schema::dropIfExists('personal');
    }
};
