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
        if (Auth::check()) {
            $usuario = Auth::user()->load('tipoUsuario');
            if ($usuario && $usuario->tipoUsuario->tipoUsuario === "RH") {
                return $next($request);
            }
        }
        return redirect()->route('inicioSesion');
    }
}