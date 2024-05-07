<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class castigo extends Model
{
    use HasFactory;
    protected $table = "castigo";
    protected $primaryKey = "idCastigo";

    protected $fillable = [
        'castigo',
        'observaciones',
        'idUnidad',
    ];
}
