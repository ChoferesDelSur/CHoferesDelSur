<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class tipoUsuario extends Model
{
    use HasFactory;
    protected $table = "tipoUsuario";
    protected $primaryKey = 'idTipoUsuario';

    protected $fillable = [
        'tipoUsuario',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(usuario::class, 'idTipoUsuario', 'idTipoUsuario');
    }
}
