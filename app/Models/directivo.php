<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class directivo extends Model
{
    use HasFactory;
    protected $table = "directivo";
    protected $primaryKey = "idDirectivo";

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'idTipoDirectivo',
        'idOperador'
    ];
}
