<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class direccion extends Model
{
    use HasFactory;
    protected $table = "direccion";
    protected $primaryKey = 'idDireccion';

    protected $fillable = [
        'calle',
        'numero',
        'idAsentamiento',
    ];

    public function operador(): BelongsToMany
    {
        return $this->belongsToMany(operador::class, 'idDireccion', 'idDireccion');
    }

    public function asentamiento(): HasOne
    {
        return $this->hasOne(asentamiento::class, 'idAsentamiento', 'idAsentamiento');
    }
    
    protected function calle(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucwords($value), //Funcion flecha (Como en JavaScript), Laravel > 8
            set: function($value){
                return strtolower($value);
            }
        );
    }
}
