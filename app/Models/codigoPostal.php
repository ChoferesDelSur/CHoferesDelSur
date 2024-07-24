<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class codigoPostal extends Model
{
    use HasFactory;

    protected $table = "codigoPostal";
    protected $primaryKey = 'idCodigoPostal';

    protected $fillable = [
        'codigoPostal',
    ];

    public function asentamientos(): HasMany
    {
        return $this->hasMany(asentamiento::class, 'idCodigoPostal', 'idCodigoPostal');
    }
}
