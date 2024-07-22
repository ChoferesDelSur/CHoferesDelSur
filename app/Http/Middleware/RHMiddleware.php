<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RHMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Dentro de RHMiddleware');
        if (Auth::check()) {
            $usuario = Auth::user()->load('tipoUsuario');
            Log::info('Usuario autenticado', ['usuario' => $usuario]);
            if ($usuario && $usuario->tipoUsuario->tipoUsuario === "RH") {
                Log::info('Usuario es RH, permitiendo acceso');
                return $next($request);
            }
        }
        Log::info('Redirigiendo a inicioSesion desde RHMiddleware');
        return redirect()->route('inicioSesion');
    }
}