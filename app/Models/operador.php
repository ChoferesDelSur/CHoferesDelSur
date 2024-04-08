<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class operador extends Model
{
    use HasFactory;
    protected $table = "operador";
    protected $primaryKey = "idOperador";

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'idEstado',
        'idTipoOperador',
        'idAsistencia'
    ];
}
