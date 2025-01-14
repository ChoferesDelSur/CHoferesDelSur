<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estado extends Model
{
    use HasFactory;
    protected $table = "estado";
    protected $primaryKey = "idEstado";

    protected $fillable = [
        'estado'
    ];

    public function tipoMovimientos()
    {
        return $this->hasMany(tipoMovimiento::class, 'idEstado');
    }
}
