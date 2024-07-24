<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class municipio extends Model
{
    use HasFactory;
    protected $table = "municipio";
    protected $primaryKey = "idMunicipio";

    protected $fillable = [
        'municipio',
        'idEntidad',
    ];

    public function estados(): BelongsTo
    {
        return $this->belongsTo(entidad::class, 'idEntidad', 'idEntidad');
    }

    public function codigoPostal(): BelongsToMany
    {
        return $this->belongsToMany(codigoPostal::class, 'idMunicipio', 'idMunicipio');
    }

    protected function municipio(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucwords($value), //Funcion flecha (Como en JavaScript), Laravel > 8
            set: function($value){
                return strtolower($value);
            }
        );
    }

    public function asentamientos(): HasMany
    {
        return $this->hasMany(asentamiento::class, 'idMunicipio', 'idMunicipio');
    }
}
