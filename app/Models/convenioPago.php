<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class convenioPago extends Model
{
    use HasFactory;
    protected $table = "convenioPago";
    protected $primaryKey = 'idConvenioPago';

    protected $fillable = [
        'convenioPago',
    ];
}
