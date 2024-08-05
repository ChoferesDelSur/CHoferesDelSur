<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class escolaridad extends Model
{
    use HasFactory;
    protected $table = "escolaridad";
    protected $primaryKey = 'idEscolaridad';

    protected $fillable = [
        'escolaridad',
    ];
}
