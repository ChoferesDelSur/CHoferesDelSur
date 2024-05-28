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
        'ultimaCorrida',
        'horaInicioUC',
        'horaFinUC',
        'idUnidad',
    ];
}
