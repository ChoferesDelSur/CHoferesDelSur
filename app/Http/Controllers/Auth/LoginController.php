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
        \Log::info('Esta en la funcion index');
        if (auth()->check()) {
            $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->get();
            $tipoUsuario = $usuario[0]->tipoUsuario->tipoUsuario;
            \Log::info('Esta por entrar en el switch de tipoUsuario de index');
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
            return Inertia::render('Autenticacion/RegisterFT');    
        }
        \Log::info('Rendering component: Autenticacion/Login');
        return Inertia::render('Autenticacion/Login',[
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        \Log::info('Estoy dentro de la funcion login');
        try {
            $request->validate([
                'usuario' => ['required'],
                'password' => ['required'],
            ]);

            $remember = $request->remember;
            $user = usuario::where('usuario', $request->usuario)->first();
            if ($user) {
                if ($user->cambioContrasenia === 0) {
                    if (Carbon::parse($user->fecha_Creacion)->addHours(48) <= Carbon::now()) {
                        return back()->with(['message' => 'Excedio el tiempo limite para el cambio de contraseña, para solucionarlo es necesario que acuda a la dirección', 'color' => 'red', 'type' => 'error']);
                    }
                }
                if ($user->intentos > 0) {
                    if ($user && Hash::check($request->password, $user->password)) {
                        $user->intentos = 10;
                        $user->save();
                        Auth::login($user, $remember);                        
                        $request->session()->regenerate();

                        $usuario = usuario::where('idUsuario', auth()->user()->idUsuario)->with(['tipoUsuario'])->get();
                        $tipoUsuario = $usuario[0]->tipoUsuario->tipoUsuario;
                        \Log::info('Está por ingresar al switch de los dos tipos de usuario');
                        switch ($tipoUsuario) {
                            
                            case "Administrador":
                                return redirect()->intended(route('principal.inicio'));
                                break;
                            case "Servicio":
                                return redirect()->intended(route('servicio.inicio'));
                                break;
                        }
                    }

                    $user->intentos = $user->intentos - 1;
                    $user->save();
                    if ($user->intentos != 0) {
                        return back()->with(['message' => 'Credenciales incorrectas, tiene solo ' . $user->intentos . ' intentos para poder acceder a su cuenta.', 'color' => 'yellow', 'type' => 'info']);
                    }
                    return back()->with(['message' => 'Intentos maximos de inicio de sesión superados. Para poder acceder a su cuenta es necesario comunicarse con el administrador para su desbloqueo.', 'color' => 'red','type' => 'error']);
                }
                return back()->with(['message' => 'Intentos maximos de inicio de sesión superados. Para poder acceder a su cuenta es necesario comunicarse con el administrador para su desbloqueo.', 'color' => 'red', 'type' => 'error']);
            }
            return back()->with(['message' => 'No existe el usuario ingresado', 'color' => 'red', 'type' => 'error']);
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
