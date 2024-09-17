<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class rolServicio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "rolServicio";
    protected $primaryKey = "idRolServicio";

    protected $fillable = [
        'trabajaDomingo',
        'idUnidad',
    ];
}
