<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ultimaCorrida extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "ultimaCorrida";
    protected $primaryKey = "idUltimaCorrida";

    protected $fillable = [
        'horaInicioUC',
        'horaFinUC',
        'idUnidad',
        'idTipoUltimaCorrida',
        'idOperador'
    ];

    public function unidad()
    {
        return $this->belongsTo(unidad::class, 'idUnidad');
    }
    
    public function operador()
    {
        return $this->belongsTo(operador::class, 'idOperador');
    }

    public function tipoUltimaCorrida()
    {
        return $this->belongsTo(tipoUltimaCorrida::class, 'idTipoUltimaCorrida');
    }
}
