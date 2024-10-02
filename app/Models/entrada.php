<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class entrada extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "entrada";
    protected $primaryKey = "idEntrada";

    protected $fillable = [
        'horaEntrada',
        'tipoEntrada',
        'extremo',
        'idUnidad',
        'idRuta',
        'idDirectivo',
        'idOperador'
    ];

    public function unidad()
    {
        return $this->belongsTo(unidad::class, 'idUnidad');
    }

    public function directivo(){
        return $this->belongsTo(directivo::class, 'idDirectivo');
    }
    
    public function operador()
    {
        return $this->belongsTo(operador::class, 'idOperador');
    }

    public function ruta(){
        return $this->belongsTo(ruta::class, 'idRuta');
    }
}
