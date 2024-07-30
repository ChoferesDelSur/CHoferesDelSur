<?php

namespace App\Http\Controllers;

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
use App\Models\incapacidad;
use App\Models\tipoUsuario;
use App\Models\convenioPago;
use App\Models\codigoPostal;
use App\Models\entrada;
use App\Models\empresa;
use App\Models\direccion;
use App\Models\rolServicio;
use App\Models\ultimaCorrida;
use App\Models\tipoUltimaCorrida;
use Illuminate\Http\Request;
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
    }

    public function obtenerTipoUsuario($idTipoUsuario)
    {
        return tipoUsuario::find($idTipoUsuario);
    }

    public function obtenerInfoUsuario()
    {
        $idUsuario = auth()->user()->idUsuario;
        $usuario = usuario::find($idUsuario);
        $usuario->tipoUsuario3 = $usuario->tipoUsuario->tipoUsuario;

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

    public function perfil()
    {
        try {
            $usuario = $this->obtenerInfoUsuario();

            return Inertia::render('RH/Perfil', [
                'usuario' => $usuario,
                'message' => session('message'),
                'color' => session('color'),
                'type' => session('type'),
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function actualizarContrasenia(Request $request)
    {
        try {
            $usuario = usuario::find($request->idUsuario);
            $user = Auth::user();
            if (Hash::check($request->password_actual, $user->password)) {
                $usuario->contrasenia = $request->password_nueva;
                $usuario->password = bcrypt($request->password_nueva);
                $usuario->cambioContrasenia = 1;
                $usuario->save();

                return redirect()->route('rh.perfil')->With(["message" => "Contraseña actualizada correctamente, recuerde su contraseña: " . $usuario->contrasenia, "color" => "green",'type' => 'success']);
            }
            return redirect()->route('rh.perfil')->With(["message" => "Contraseña actual incorrecta", "color" => "red",'type' => 'error']);
        } catch (Exception $e) {
            return redirect()->route('rh.perfil')->With(["message" => "Error al actualizar contraseña", "color" => "red",'type' => 'error']);
            dd($e);
        }
    }

    public function operadores(){
        $operador = operador::with('direccion.asentamiento.municipio.estados','direccion.asentamiento.codigoPostal')->get();
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        $directivo = directivo::all();
        $incapacidad = incapacidad::all();
        $codigoPostal = codigoPostal::all();
        $direccion = direccion::with('asentamiento.municipio.estados', 'asentamiento.codigoPostal')->get();
        $empresa = empresa::all();
        $convenioPago = convenioPago::all();
        $usuario = $this->obtenerInfoUsuario();
        return Inertia::render('RH/Operadores',[
            'usuario' => $usuario,
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'estado' => $estado,
            'incapacidad' => $incapacidad,
            'directivo' => $directivo,
            'empresa' => $empresa,
            'convenioPago' => $convenioPago,
            'codigoPostal' => $codigoPostal,
            'direccion' => $direccion,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    public function addOperador(Request $request){
        try{
            $request->validate([
                'nombre'=> 'required',
                'apellidoP'=> 'required',
                'apellidoM' => 'required',
                'fechaNacimiento' => 'required',
                'edad' => 'required',
                'CURP' => 'required',
                'RFC' => 'required',
                'numTelefono' => 'required',
                'NSS' => 'required',
                'tipoOperador' => 'required',
                'estado' => 'required',
                'fechaAlta' => 'required',
                'fechaBaja' => 'nullable',
                'empresaa' => 'required',
                'convenioPa'=> 'required',
                'antiguedad' => 'required',
                'numLicencia' => 'required',
                'vigenciaLicencia' => 'required',
                'numINE' => 'required',
                'vigenciaINE' => 'required',
                'constanciaSF' => 'nullable|boolean',
                'cursoSEMOVI' => 'nullable|boolean',
                'constanciaSEMOVI' => 'nullable|boolean',
                'cursoPsicologico' => 'nullable|boolean',
                'ultimoContrato' => 'required',
                'directivo' => 'required',
                // Validación de campos de incapacidad
                'motivo' => 'nullable|required_if:estado,3',
                'numeroDias' => 'nullable|required_if:estado,3|integer',
                'fechaInicio' => 'nullable|required_if:estado,3|date',
                'fechaFin' => 'nullable|required_if:estado,3|date',
            ]);
            /* dd($request->all()); */
            Log::info('Datos del Request después de la validación:', $request->all());
            // Verificar si el operador ya existe
            $existingOperador = operador::where('CURP', $request->CURP)->first();

            if($existingOperador){
            // Operador ya existe, puedes devolver una respuesta indicando el error
            return redirect()->route('rh.operadores')->with(['message' => "El operador ya está registrado: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, " con CURP:" . $request->CURP, "color" => "yellow", 'type' => 'info']);
            }

            //fechaFormateada
            $fechaFormateada = date('ymd', strtotime($request->fechaNacimiento));

            //Se guarda el domicilio del profesor
            $domicilio = new direccion();
            $domicilio->calle = $request->calle;
            $domicilio->numero = $request->numero;
            $domicilio->idAsentamiento = $request->asentamiento;
            $domicilio->save();
            Log::info('Datos después de guardar el domicilio:', ['request' => $request->all(), 'domicilio' => $domicilio]);
    
            $operador = new operador();
            $operador->nombre = $request->nombre;
            $operador->apellidoP = $request->apellidoP;
            $operador->apellidoM = $request->apellidoM;
            $operador->fechaNacimiento = $request->fechaNacimiento;
            $operador->edad = $request->edad;
            $operador->CURP = $request->CURP;               
            $operador->RFC = $request->RFC;
            $operador->numTelefono = $request->numTelefono;
            $operador->NSS = $request->NSS;

            $operador->idTipoOperador = $request->tipoOperador;
            $operador->idEstado = $request->estado;
            $operador->fechaAlta = $request->fechaAlta;
            $operador->fechaBaja = $request->fechaBaja;
            $operador->idEmpresa = $request->empresaa;//le puse empresaa como esta en mi formulario
            $operador->idConvenioPago = $request->convenioPa;
            $operador->antiguedad = $request->antiguedad;

            $operador->idDireccion = $domicilio->idDireccion;

            $operador->numLicencia = $request->numLicencia;
            $operador->vigenciaLicencia = $request->vigenciaLicencia;
            $operador->numINE = $request->numINE;
            $operador->vigenciaINE = $request->vigenciaINE;
            $operador->constanciaSF = $request->constanciaSF ?? false;
            $operador->cursoSemovi = $request->cursoSemovi ?? false;
            $operador->constanciaSemovi = $request->constanciaSemovi ?? false;
            $operador->cursoPsicologico = $request->cursoPsicologico ?? false;

            $operador->ultimoContrato = $request->ultimoContrato;
            $operador->idDirectivo = $request->directivo;

            $nombreCompleto = $operador->apellidoP . ' ' . $operador->apellidoM . ' ' . $operador->nombre;
            $operador->nombre_completo = $nombreCompleto;

            $operador->save();

            // Si el estado es "Incapacidad", registrar la incapacidad
        if ($request->estado == 3) {
            $incapacidad = new incapacidad();
            $incapacidad->motivo = $request->motivo;
            $incapacidad->numeroDias = $request->numeroDias;
            $incapacidad->fechaInicio = $request->fechaInicio;
            $incapacidad->fechaFin = $request->fechaFin;
            $incapacidad->idOperador = $operador->idOperador;
            $incapacidad->save();
        }
            
            return redirect()->route('rh.operadores')->with(['message' => "Operador agregado correctamente: $nombreCompleto", "color" => "green", 'type' => 'success']);
        }catch(Exception $e){
            Log::error('Error al agregar al operador:', ['error' => $e->getMessage()]);
            return redirect()->route('rh.operadores')->with(['message' => "Error al agregar al operador", "color" => "red", 'type' => 'error']);
        }
    }

    public function actualizarOperador(Request $request, $idOperador)
    {
        try{
            $request->validate([
                'nombre'=> 'required',
                'apellidoP'=> 'required',
                'apellidoM' => 'required',
                'tipoOperador' => 'required',
                'estado' => 'required',
                'directivo' => 'required',
            ]);
            $operador = operador::find($idOperador);
            $operador->nombre = $request->nombre;
            $operador->apellidoP = $request->apellidoP;
            $operador->apellidoM = $request->apellidoM;
            $operador->idTipoOperador = $request->tipoOperador;
            $operador->idEstado = $request->estado;
            $operador->idDirectivo = $request->directivo;

            $nombreCompleto = $operador->apellidoP . ' ' . $operador->apellidoM . ' ' . $operador->nombre;
            $operador->nombre_completo = $nombreCompleto;
            
            $operador->save();
            return redirect()->route('rh.operadores')->with(['message' => "Operador actualizado correctamente: " . $request->nombre . " " . $request->apellidoP . " " . $request->apellidoM, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('rh.operadores')->with(['message' => "El operador no se actualizó correctamente: " . $requests->nombre, "color" => "reed"]);
        }
    }

    public function eliminarOperador($operadoresIds){
        try{
            // Convierte la cadena de IDs en un array
            $operadoresIdsArray = explode(',', $operadoresIds);

            // Limpia los IDs para evitar posibles problemas de seguridad
            $operadoresIdsArray = array_map('intval', $operadoresIdsArray);

            // Elimina las materias
            operador::whereIn('idOperador', $operadoresIdsArray)->delete();
            // Redirige a la página deseada después de la eliminación
            return redirect()->route('rh.operadores')->with(['message' => "Operador eliminado correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('rh.operadores')->with(['message' => "No se pudo eliminar al operador", "color" => "red"]);
        }
    }

    public function sociosPrestadores(){
        $directivo = directivo::all();
        $operador = operador::all();
        $tipDirectivo = tipodirectivo::all();
        $usuario = $this->obtenerInfoUsuario();
        return Inertia::render('RH/SociosPrestadores',[
            'usuario' => $usuario,
            'directivo' => $directivo,
            'operador' => $operador,
            'tipDirectivo' => $tipDirectivo,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    public function addDirectivo(Request $request){
        try{
            $request->validate([
                'nombre'=> 'required',
                'apellidoP'=> 'required',
                'apellidoM' => 'required',
                'tipDirectivo' => 'required',
            ]);

            // Verificar si ya existe un directivo con el mismo nombre completo
            $nombreCompleto = $request->apellidoP . ' ' . $request->apellidoM . ' ' . $request->nombre;
            $directivoExistente = directivo::where('nombre_completo', $nombreCompleto)->first();
            
            if($directivoExistente) {
                // Si ya existe un directivo con el mismo nombre completo, retornar un mensaje de error o realizar la acción correspondiente
                return redirect()->route('rh.sociosPrestadores')->with(['message' => "El directivo ya está registrado: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, "color" => "yellow", 'type' => 'info']);
            }
    
            $directivo = new directivo();
            $directivo->nombre = $request->nombre;
            $directivo->apellidoP = $request->apellidoP;
            $directivo->apellidoM = $request->apellidoM;
            $directivo->idTipoDirectivo = $request->tipDirectivo;
            $nombreCompleto =$directivo->apellidoP . ' ' . $directivo->apellidoM. ' ' . $directivo->nombre;
            $directivo->nombre_completo = $nombreCompleto;

            $directivo->save();
            return redirect()->route('rh.sociosPrestadores')->with(['message' => "Directivo agregado correctamente: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, "color" => "green", 'type' => 'success']);
        }catch(Exception $e){
            return redirect()->route('rh.sociosPrestadores')->with(['message' => "Error al agregar al directivo", "color" => "red", 'type' => 'error']);
        }
    }

    public function actualizarDirectivo(Request $request, $idDirectivo)
    {
        try{
            $request->validate([
                'nombre'=> 'required',
                'apellidoP'=> 'required',
                'apellidoM' => 'required',
                'tipDirectivo' => 'required',
            ]);

            $directivo = directivo::find($idDirectivo);
            $directivo->nombre = $request->nombre;
            $directivo->apellidoP = $request->apellidoP;
            $directivo->apellidoM = $request->apellidoM;
            $directivo->idTipoDirectivo = $request->tipDirectivo;
            $nombreCompleto =$directivo->apellidoP . ' ' . $directivo->apellidoM. ' ' . $directivo->nombre;
            $directivo->nombre_completo = $nombreCompleto;

            $directivo->save();

            return redirect()->route('rh.sociosPrestadores')->with(['message' => "Directivo actualizado correctamente: " . $nombreCompleto, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('rh.sociosPrestadores')->with(['message' => "El directivo no se actualizó correctamente: " . $nombreCompleto, "color" => "reed"]);
        }
    }

    public function eliminarDirectivo($directivosIds){
        try{
            // Convierte la cadena de IDs en un array
            $directivosIdsArray = explode(',', $directivosIds);

            // Limpia los IDs para evitar posibles problemas de seguridad
            $directivosIdsArray = array_map('intval', $directivosIdsArray);

            // Elimina las materias
            directivo::whereIn('idDirectivo', $directivosIdsArray)->delete();
            // Redirige a la página deseada después de la eliminación
            return redirect()->route('rh.sociosPrestadores')->with(['message' => "Directivo eliminada correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('rh.sociosPrestadores')->with(['message' => "No se pudo eliminar al directivo", "color" => "red"]);
        }
    }
    public function incapacidades(){
        $incapacidad = incapacidad::all();
        $operador = operador::all();
        $usuario = $this->obtenerInfoUsuario();
        return Inertia::render('RH/Incapacidades',[
            'incapacidad' => $incapacidad,
            'usuario' => $usuario,
            'operador' => $operador,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }
}
