<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class ultimaCorrida extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "ultimaCorrida";
    protected $primaryKey = "idUltimaCorrida";

    protected $fillable = [
        'horaInicioUC',
        'horaFinUC',
        'idUnidad',
        'idTipoUltimaCorrida'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'idUnidad');
    }

    public function tipoUltimaCorrida()
    {
        return $this->belongsTo(TipoUltimaCorrida::class, 'idTipoUltimaCorrida');
    }
}
