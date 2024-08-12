<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class movimiento extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "movimiento";
    protected $primaryKey = "idMovimiento";

    protected $fillable = [
        'fechaMovimiento',
        'idEstado',
        'idTipoMovimiento',
        'idDirectivo',
        'idOperador',
    ];

    // Relación con el modelo operador
    public function operador()
    {
        return $this->belongsTo(operador::class, 'idOperador', 'idOperador');
    }

    // Relación con el modelo directivo
    public function directivo()
    {
        return $this->belongsTo(directivo::class, 'idDirectivo', 'idDirectivo');
    }

    // Relación con el modelo tipoMovimiento
    public function tipoMovimiento()
    {
        return $this->belongsTo(tipoMovimiento::class, 'idTipoMovimiento', 'idTipoMovimiento');
    }

    // Relación con el modelo estado
    public function estado()
    {
        return $this->belongsTo(estado::class, 'idEstado', 'idEstado');
    }
}
