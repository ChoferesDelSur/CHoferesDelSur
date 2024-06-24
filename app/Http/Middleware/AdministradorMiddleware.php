<?php

namespace App\Http\Middleware;

use App\Models\usuario;
use Closure;
use Illuminate\Http\Request;
/* use Illuminate\Support\Facades\Auth; */
use Illuminate\Support\Facades\Log;     
use Symfony\Component\HttpFoundation\Response;

class AdministradorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('Estoy en funcion handle de AdministradorMiddleware');
        if(auth()->check()){
            \Log::info('Estoy en el if del check',\auth);
            console.log("Estoy en handle dentro de AdministradorMiddleware");
            console.log("auth:",\auth);
            $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->first();
            if($usuario && $usuario->tipoUsuario && $usuario->tipoUsuario->tipoUsuario==='Administrador'){
                return $next($request);
            }
        }
        return redirect()->route('inicioSesion');
    }
}
