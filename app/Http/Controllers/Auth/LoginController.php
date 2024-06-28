<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\tipoUsuario;
use App\Models\usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->get();
            $tipoUsuario = $usuario[0]->tipoUsuario->tipoUsuario;
            switch ($tipoUsuario) {
                case "Administrador":
                    return redirect()->route('principal.inicio');
                    break;
                case "Servicio":
                    return redirect()->route('servicio.inicio');
                    break;
            }
        }
        $tipoUsuario = tipoUsuario::where('tipoUsuario','Administrador')->first();
        $usuario = usuario::where('idTipoUsuario', $tipoUsuario->idTipoUsuario)->get();
        if($usuario->isEmpty()){
            return Inertia::render('Login/RegisterFT');    
        }
        return Inertia::render('Login/Login',[
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'usuario' => ['required'],
                'password' => ['required'],
            ]);

            $remember = $request->remember;
            $user = Usuario::where('usuario', $request->usuario)->first();

            if ($user) {
                if ($user->cambioContrasenia === 0) {
                    if (Carbon::parse($user->fecha_Creacion)->addHours(48) <= Carbon::now()) {
                        return back()->with([
                            'message' => 'Excedió el tiempo límite para el cambio de contraseña.',
                            'color' => 'red',
                            'type' => 'error'
                        ]);
                    }
                }

                if ($user->intentos > 0) {
                    if (Hash::check($request->password, $user->password)) {
                        $user->intentos = 10;
                        $user->save();
                        Auth::login($user, $remember);
                        $request->session()->regenerate();

                        // Obtener tipo de usuario
                        $tipoUsuario = $user->tipoUsuario->tipoUsuario;

                        // Redirigir según el tipo de usuario
                        switch ($tipoUsuario) {
                            case "Administrador":
                                return redirect()->intended(route('principal.inicio'));
                                break;
                            case "Servicio":
                                return redirect()->intended(route('servicio.inicio'));
                                break;
                            default:
                                return redirect()->intended(route('login'))->with([
                                    'message' => 'Tipo de usuario no reconocido.',
                                    'color' => 'red',
                                    'type' => 'error'
                                ]);
                        }
                    } else {
                        $user->intentos = $user->intentos - 1;
                        $user->save();
                        if ($user->intentos != 0) {
                            return back()->with([
                                'message' => 'Credenciales incorrectas. Tiene ' . $user->intentos . ' intentos restantes.',
                                'color' => 'yellow',
                                'type' => 'info'
                            ]);
                        }
                        return back()->with([
                            'message' => 'Intentos máximos de inicio de sesión superados. Comuníquese con el administrador.',
                            'color' => 'red',
                            'type' => 'error'
                        ]);
                    }
                }
                return back()->with([
                    'message' => 'Intentos máximos de inicio de sesión superados. Comuníquese con el administrador.',
                    'color' => 'red',
                    'type' => 'error'
                ]);
            }

            return back()->with([
                'message' => 'Usuario no encontrado.',
                'color' => 'red',
                'type' => 'error'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('inicioSesion');
    }

    public function register(Request $request)
    {
        try{
            $request->validate([
                'nombre' => ['required'],
                'apellidoP' => ['required'],
                'apellidoM' => ['required'],
                'usuario' => ['required'],
                'password' => ['required'],
            ]);
            $tipoUs = tipoUsuario::where('tipoUsuario', 'Administrador')->first();            
            $usuario = new usuario();
            $usuario->nombre = $request->nombre;
            $usuario->apellidoP = $request->apellidoP;
            $usuario->apellidoM = $request->apellidoM;
            $usuario->usuario = $request->usuario;
            $usuario->contrasenia = $request->password;
            $usuario->password = bcrypt($request->password);
            $usuario->cambioContrasenia = true;        
            $usuario->idTipoUsuario = $tipoUs->idTipoUsuario;
            $usuario->save();
            return redirect()->route('inicioSesion');
        }catch(Exception $e){
            dd($e);
        }
    }
}