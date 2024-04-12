<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class corte extends Model
{
    use HasFactory;
    protected $table = "corte";
    protected $primaryKey = "idCorte";

    protected $fillable = [
        'horaCorte',
        'causa',
        'horaRegreso',
        'notaTalacha',
        'tiempoCorte',
        'tipoCorte',
        'idCastigo',
    ];
}
