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
        'fecha',
        'idUnidad',
        'idEntrada',
        'idCorte',
    ];
}
