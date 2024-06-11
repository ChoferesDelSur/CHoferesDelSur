<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class castigo extends Model
{
    use HasFactory;
    protected $table = "castigo";
    protected $primaryKey = "idCastigo";

    protected $fillable = [
        'castigo',
        'observaciones',
        'horaInicio',
        'horaFin',
        'idUnidad',
        'idOperador'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'idUnidad');
    }
    
    public function operador()
    {
        return $this->belongsTo(Operador::class, 'idOperador');
    }
}
