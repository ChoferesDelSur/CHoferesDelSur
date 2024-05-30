<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoUltimaCorrida extends Model
{
    use HasFactory;
    protected $table = "tipoUltimaCorrida";
    protected $primaryKey = "idTipoUltimaCorrida";

    protected $fillable = [
        'tipoUltimaCorrida'
    ];
}
