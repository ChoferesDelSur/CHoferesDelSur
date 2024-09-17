<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ruta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "ruta";
    protected $primaryKey = "idRuta";

    protected $fillable = [
        'nombreRuta'
    ];

}
