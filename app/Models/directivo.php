<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class directivo extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "directivo";
    protected $primaryKey = "idDirectivo";

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'idTipoDirectivo',
        'idOperador',
        'numUnidades',
        'numOperadores',
        'nombre_completo',
    ];
}
