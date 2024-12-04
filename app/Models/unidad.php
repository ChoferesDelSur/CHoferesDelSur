<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class unidad extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        return $this->hasMany(rolServicio::class, 'idUnidad');
    }

    public function entradas()
    {
        return $this->hasMany(entrada::class, 'idUnidad');
    }

    public function cortes()
    {
        return $this->hasMany(corte::class, 'idUnidad');
    }
    public function ruta()
    {
        return $this->belongsTo(ruta::class, 'idRuta');
    }

    public function directivo()
    {
        return $this->belongsTo(directivo::class, 'idDirectivo');
    }

        public function castigos()
    {
        return $this->hasMany(castigo::class, 'idUnidad');
    }

        public function ultimaCorridas()
    {
        return $this->hasMany(ultimaCorrida::class, 'idUnidad');
    }

}