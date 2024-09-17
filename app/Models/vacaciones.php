<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class vacaciones extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "vacaciones";
    protected $primaryKey = "idVacaciones";

    protected $fillable = [
        'motivo',
        'numeroDias',
        'fechaInicio',
        'fechaFin',
        'idPersonal',
    ];
}
