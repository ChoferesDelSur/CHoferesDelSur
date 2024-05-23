<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class ruta extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = "ruta";
    protected $primaryKey = "idRuta";

    protected $fillable = [
        'nombreRuta'
    ];

}
