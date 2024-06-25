<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info("Estoy en AdminMiddleware");
        error_log("Estoy en AdminMiddleware");
        if(auth()->check()){
            Log::info("Auth",auth());
            $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->first();
            if($usuario && $usuario->tipoUsuario && $usuario->tipoUsuario->tipoUsuario==='Administrador'){
                Log::info('Usuario es administrador. Permitiendo acceso.');
                return $next($request);
            }
        }
        return redirect()->route('inicioSesion');
    }
}
