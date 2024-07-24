<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class entidad extends Model
{
    use HasFactory;

    protected $table = "entidad";
    protected $primaryKey = 'idEntidad';

    protected $fillable = [
        'entidad',
    ];

    public function direcciones(): BelongsToMany
    {
        return $this->belongsToMany(direccion::class, 'idEntidad', 'idEntidad');
    }

    public function codigoPostal(): BelongsToMany
    {
        return $this->belongsToMany(codigoPostal::class, 'idEntidad', 'idEntidad');
    }
    
    public function municipios(): HasMany
    {
        return $this->hasMany(municipio::class, 'idEntidad', 'idEntidad');
    }
}
