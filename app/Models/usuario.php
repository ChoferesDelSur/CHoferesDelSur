<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class usuarios extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use SoftDeletes;

    protected $table = "usuario";
    protected $primaryKey = 'idUsuario';
    protected $username = 'usuario';
    protected $password = 'password';

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'usuario',
        'contrasenia',
        'idTipoUsuario',
        'intentos',
        'fecha_Creacion',
        'cambioContrasenia',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthIdentifier()
    {
        return $this->attributes['idUsuario'];
    }
}
