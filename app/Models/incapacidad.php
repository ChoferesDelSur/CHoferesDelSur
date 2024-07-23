<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class incapacidad extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "incapacidad";
    protected $primaryKey = "idIncapacidad";

    protected $fillable = [
        'motivo',
        'fechaInicio',
        'fechaFin',
        'idOperador',
    ];
}
