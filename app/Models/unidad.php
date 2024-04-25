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
        'idOperador'
    ];

    public function operador()
{
    return $this->belongsTo(Operador::class, 'idOperador');
}

}
