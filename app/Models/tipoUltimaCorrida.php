<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tipoUltimaCorrida extends Model
{
    use HasFactory;
    protected $table = "tipoUltimaCorrida";
    protected $primaryKey = "idTipoUltimaCorrida";

    protected $fillable = [
        'tipoUltimaCorrida'
    ];
    public function ultimaCorrida(): BelongsToMany
    {
        return $this->belongsToMany(ultimaCorrida::class, 'idTipoUltimaCorrida', 'idTipoUltimaCorrida');
    }
}
