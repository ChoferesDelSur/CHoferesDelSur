<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoMovimiento extends Model
{
    use HasFactory;
    protected $table = "tipoMovimiento";
    protected $primaryKey = "idTipoMovimiento";

    protected $fillable = [
        'tipoMovimiento',
        'idEstado'
    ];

    public function movimientos()
    {
        return $this->hasMany(movimiento::class, 'idTipoMovimiento', 'idTipoMovimiento');
    }


}
