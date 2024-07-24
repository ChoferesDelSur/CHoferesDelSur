<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class operador extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "operador";
    protected $primaryKey = "idOperador";

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'fechaNacimiento',
        'edad',
        'CURP',
        'RFC',
        'telefono',
        'NSS',
        'idEstado',
        'idTipoOperador',
        'idDireccion',
        'numLicencia',
        'vigenciaLicencia',
        'numINE',
        'vigenciaINE',
        'ultimoContrato',
        'antiguedad',
        'fechaAlta',
        'fechaBaja',
        'idEmpresa',
        'constanciaSF',
        'idConvenioPago',
        'cursoSemovi',
        'nombre_completo'
    ];

    public function unidad()
{
    return $this->hasOne(Unidad::class, 'idOperador');
}

public function direcciones(): HasOne
    {
        return $this->hasOne(direccion::class, 'idDireccion', 'idDireccion');
    }

}