<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoDirectivo extends Model
{
    use HasFactory;
    protected $table = "tipoDirectivo";
    protected $primaryKey = "idTipoDirectivo";

    protected $fillable = [
        'tipoDirectivo'
    ];
}
