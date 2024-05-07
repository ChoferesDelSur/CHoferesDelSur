<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class formacionUnidades extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "formacionUnidades";
    protected $primaryKey = "idFormacionUnidades";

    protected $fillable = [
        'idUnidad',
        'horaEntrada',
        'tipoEntrada',
        'extremo',
        'horaCorte',
        'causa',
        'horaRegreso',
        'ultimaCorrida',
        'horaInicioUC',
        'horaFinUC',
        'idCastigo',
    ];

    // Método para actualizar el campo tipoEntrada
    public function actualizarTipoEntrada($tipoEntrada)
    {
        $this->tipoEntrada = $tipoEntrada;
        $this->save();
    }
}
