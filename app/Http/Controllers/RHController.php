<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\unidad;
use App\Models\directivo;
use App\Models\operador;
use App\Models\calificacion;
use App\Models\estado;
use App\Models\ruta;
use App\Models\tipodirectivo;
use App\Models\tipooperador;
use App\Models\castigo;
use App\Models\corte;
use App\Models\usuario;
use App\Models\tipoUsuario;
use App\Models\entrada;
use App\Models\rolServicio;
use App\Models\ultimaCorrida;
use App\Models\tipoUltimaCorrida;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RHController extends Controller
{
    public function obtenerUsuario()
    {
        return auth()->user();
        Log::info('Usuario obtenido:', ['user' => $user]);
    }

    public function obtenerTipoUsuario($idTipoUsuario)
    {
        return tipoUsuario::find($idTipoUsuario);
        Log::info('Tipo de Usuario obtenido:', ['tipoUsuario' => $tipoUsuario]);
    }

    public function obtenerInfoUsuario()
    {
        $idUsuario = auth()->user()->idUsuario;
        $usuario = usuario::find($idUsuario);
        $usuario->tipoUsuario3 = $usuario->tipoUsuario->tipoUsuario;
        Log::info('Información del Usuario:', ['usuario' => $usuario]);
        return $usuario;
    }

    public function inicio()
    {
        $usuario = $this->obtenerInfoUsuario();
        if ($usuario->cambioContrasenia === 0) {
            $fechaLimite = Carbon::parse($usuario->fecha_Creacion)->addHours(48);
            $fechaFormateada = $fechaLimite->format('d/m/Y');
            $horaFormateada = $fechaLimite->format('H:i');
            $message = "Tiene hasta el " . $fechaFormateada . " a las " . $horaFormateada . " hrs para realizar el cambio de contraseña, en caso contrario, esta se desactivará y será necesario comunicarse con el administrador para solucionar la situación";
            $color = "red";
            return Inertia::render('RH/Inicio',[
                'usuario' => $usuario,
                'message' => $message /* session('message') */,
                'color' => $color,
                'type' => session('type'),
            ]);
        }
        return Inertia::render('RH/Inicio',[
            'usuario' => $usuario,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }
}
