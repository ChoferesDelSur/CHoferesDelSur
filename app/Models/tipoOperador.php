<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoOperador extends Model
{
    use HasFactory;
    protected $table = "tipoOperador";
    protected $primaryKey = "idTipoOperador";

    protected $fillable = [
        'tipOperador'
    ];
}
