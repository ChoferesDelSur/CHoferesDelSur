<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class asentamiento extends Model
{
    use HasFactory;
    protected $table = 'asentamiento';
    protected $primaryKey = 'idAsentamiento';

    protected $fillable = [
        'asentamiento',
        'tipo',
        'idMunicipio',
        'idCodigoPostal'
    ];

    public function municipios(): BelongsTo
    {
        return $this->belongsTo(municipio::class, 'idMunicipio', 'idMunicipio');
    }

    public function codigoPostal(): BelongsTo
    {
        return $this->belongsTo(codigoPostal::class, 'idCodigoPostal', 'idCodigoPostal');
    }

    public function direcciones(): BelongsToMany
    {
        return $this->belongsToMany(direccion::class, 'idAsentamiento', 'idAsentamiento');
    }
}
