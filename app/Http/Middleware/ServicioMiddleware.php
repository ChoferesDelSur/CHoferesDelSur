<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicioMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info("Estoy en ServicioMiddleware");
        if(auth()->check()){
            Log::info("Auth", ['user' => auth()->user()]);
            $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->get();
            $tipoUsuario = $usuario[0]->tipoUsuario->tipoUsuario;
            if ($tipoUsuario === "Servicio") {
                return $next($request);
            }
        }
        return redirect()->route('inicioSesion');
    }
}
