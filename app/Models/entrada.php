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
