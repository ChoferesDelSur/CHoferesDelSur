<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class personal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "personal";
    protected $primaryKey = "idPersonal";

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'fechaNacimiento',
        'edad',
        'CURP',
        'RFC',
        'numTelefono',
        'telEmergencia',
        'NSS',
        'idDireccion',
        'numINE',
        'vigenciaINE',
        'antiguedad',
        'fechaAlta',
        'fechaBaja',
        'idEmpresa',
        'idEscolaridad',
        'constanciaSF',
        'totalDiasVac',
        'diasVacRestantes',
        'nombre_completo'
    ];

    public function direccion(): HasOne
    {
        return $this->hasOne(direccion::class, 'idDireccion', 'idDireccion');
    }
}
