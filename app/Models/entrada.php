<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class entrada extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "entrada";
    protected $primaryKey = "idEntrada";

    protected $fillable = [
        'horaEntrada',
        'tipoEntrada',
        'extremo',
        'idUnidad',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'idUnidad');
    }
}
