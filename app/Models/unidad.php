<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class unidad extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "unidad";
    protected $primaryKey = "idUnidad";

    protected $fillable = [
        'numeroUnidad',
        'idRuta',
        'idOperador',
        'idDirectivo'
    ];

    public function operador()
    {
        return $this->belongsTo(operador::class, 'idOperador');
    }
    public function rolServicio()
    {
        return $this->hasOne(RolServicio::class, 'idUnidad');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'idUnidad');
    }

    public function cortes()
    {
        return $this->hasMany(Corte::class, 'idUnidad');
    }
}