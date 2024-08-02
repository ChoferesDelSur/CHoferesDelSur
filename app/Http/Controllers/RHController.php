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
        $operador = operador::all();
        //$operador = operador::with('direccion.asentamiento.municipio.estados','direccion.asentamiento.codigoPostal')->get();
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        $directivo = directivo::all();
        $incapacidad = incapacidad::all();
        $codigoPostal = codigoPostal::all();
        $direccion = direccion::all();
        //$direccion = direccion::with('asentamiento.municipio.estados', 'asentamiento.codigoPostal')->get();
        // Aquí se ajusta el operadorDireccion a cada operador y agrega propiedades al operador
        $operador->each(function($operador) use ($direccion) {
            $domicilio = $direccion->where('idDireccion', $operador->idDireccion)->first();
            $operador->domicilio = $domicilio ? $domicilio->calle . " #" . $domicilio->numero . ", " . $domicilio->asentamiento->asentamiento . ", " . $domicilio->asentamiento->municipio->municipio . ", " . $domicilio->asentamiento->municipio->estados->entidad . ", " . $domicilio->asentamiento->codigoPostal->codigoPostal : null;
            $operador->calle = $domicilio ? $domicilio->calle : null;
            $operador->numero = $domicilio ? $domicilio->numero : null;
            $operador->codigoPostal = $domicilio ? $domicilio->asentamiento->codigoPostal->codigoPostal : null;
            $operador->idAsentamiento = $domicilio ? $domicilio->asentamiento->idAsentamiento : null;
            $operador->idMunicipio = $domicilio ? $domicilio->asentamiento->municipio->idMunicipio : null;
            $operador->idEntidad = $domicilio ? $domicilio->asentamiento->municipio->estados->idEntidad : null;
        });
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
        try {
            // Validaciones básicas
            $request->validate([
                'nombre' => 'required',
                'apellidoP' => 'required',
                'apellidoM' => 'required',
                'tipoOperador' => 'required',
                'estado' => 'required',
                'directivo' => 'required',
                'fechaNacimiento' => 'required',
                'edad' => 'required',
                'CURP' => 'required',
                'RFC' => 'required',
                'numTelefono' => 'required',
                'NSS' => 'required',
                'fechaAlta' => 'required',
                'fechaBaja' => 'nullable',
                'empresaa' => 'required',
                'convenioPa' => 'required',
                'antiguedad' => 'required',
                'numLicencia' => 'required',
                'vigenciaLicencia' => 'required',
                'vigenciaINE' => 'required',
                'constanciaSF' => 'nullable|boolean',
                'cursoSemovi' => 'nullable|boolean',
                'constanciaSemovi' => 'nullable|boolean',
                'cursoPsicologico' => 'nullable|boolean',
                'ultimoContrato' => 'required',
            ]);

            // Verificar si el estado es 'Incapacidad'
            if ($request->estado == 3) {
                return redirect()->route('rh.operadores')->with(['message' => 'Para cambiar el estado del operador a Incapacidad, dirígese a la opción Incapacidades de su menú de opciones y poder registrar una incapacidad para el operador.', 'color' => 'yellow', 'type' => 'info']);
            }
            // Encontrar el operador
            $operador = operador::find($idOperador);
            // Actualizar la dirección si es necesario
            if ($request->has('calle') || $request->has('numero') || $request->has('asentamiento')) {
                $domicilio = direccion::find($operador->idDireccion);
                if ($domicilio) {
                    $domicilio->calle = $request->calle ?? $domicilio->calle;
                    $domicilio->numero = $request->numero ?? $domicilio->numero;
                    $domicilio->idAsentamiento = $request->asentamiento ?? $domicilio->idAsentamiento;
                    $domicilio->save();
                }
            }
            // Determinar la fecha actual
            $fechaActual = now();
            // Lógica para actualizar fechas según el estado
            if ($request->estado == 1) { // Estado 'Alta'
                $operador->fechaAlta = $fechaActual;
                /* $operador->fechaBaja = null; */ // Limpiar fechaBaja si se cambia a Alta
            } elseif ($request->estado == 2) { // Estado 'Baja'
                $operador->fechaBaja = $fechaActual;
            }
            // Actualizar el operador
            $operador->nombre = $request->nombre;
            $operador->apellidoP = $request->apellidoP;
            $operador->apellidoM = $request->apellidoM;
            $operador->idTipoOperador = $request->tipoOperador;
            $operador->idEstado = $request->estado;
            $operador->idDirectivo = $request->directivo;
            // Campos adicionales
            $operador->fechaNacimiento = $request->fechaNacimiento ?? $operador->fechaNacimiento;
            $operador->edad = $request->edad ?? $operador->edad;
            $operador->CURP = $request->CURP ?? $operador->CURP;
            $operador->RFC = $request->RFC ?? $operador->RFC;
            $operador->numTelefono = $request->numTelefono ?? $operador->numTelefono;
            $operador->NSS = $request->NSS ?? $operador->NSS;
            $operador->idEmpresa = $request->empresaa ?? $operador->idEmpresa;
            $operador->idConvenioPago = $request->convenioPa ?? $operador->idConvenioPago;
            $operador->antiguedad = $request->antiguedad ?? $operador->antiguedad;
            $operador->numLicencia = $request->numLicencia ?? $operador->numLicencia;
            $operador->vigenciaLicencia = $request->vigenciaLicencia ?? $operador->vigenciaLicencia;
            $operador->numINE = $request->numINE ?? $operador->numINE;
            $operador->vigenciaINE = $request->vigenciaINE ?? $operador->vigenciaINE;
            $operador->constanciaSF = $request->constanciaSF ?? $operador->constanciaSF;
            $operador->cursoSemovi = $request->cursoSemovi ?? $operador->cursoSemovi;
            $operador->constanciaSemovi = $request->constanciaSemovi ?? $operador->constanciaSemovi;
            $operador->cursoPsicologico = $request->cursoPsicologico ?? $operador->cursoPsicologico;
            $operador->ultimoContrato = $request->ultimoContrato ?? $operador->ultimoContrato;
            // Actualizar el nombre completo
            $operador->nombre_completo = $operador->apellidoP . ' ' . $operador->apellidoM . ' ' . $operador->nombre;
    
            $operador->save();
    
            return redirect()->route('rh.operadores')->with(['message' => "Operador actualizado correctamente: " . $request->nombre . " " . $request->apellidoP . " " . $request->apellidoM, "color" => "green", 'type' => 'success']);
        } catch (Exception $e) {
            Log::error('Error al actualizar al operador:', ['error' => $e->getMessage()]);
            return redirect()->route('rh.operadores')->with(['message' => "Error al actualizar al operador", "color" => "red", 'type' => 'error']);
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
        $operadoresAlta = operador::where('idEstado', 1)->get();
        $operadoresIncapacidad = operador::where('idEstado', 3)->get();
        $usuario = $this->obtenerInfoUsuario();
        return Inertia::render('RH/Incapacidades',[
            'incapacidad' => $incapacidad,
            'usuario' => $usuario,
            'operador' => $operador,
            'operadoresAlta' => $operadoresAlta,
            'operadoresIncapacidad' => $operadoresIncapacidad,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    public function addIncapacidad(Request $request){
        try{
            $request->validate([
                'motivo' => 'required',
                'numeroDias' => 'required',
                'fechaInicio' => 'required',
                'fechaFin' => 'required',
                'chofer' => 'required|exists:operador,idOperador', // Verifica que el operador exista
            ]);
            // Crear un nuevo registro de incapacidad
            incapacidad::create([
                'motivo' => $request->input('motivo'),
                'numeroDias' => $request->input('numeroDias'),
                'fechaInicio' => $request->input('fechaInicio'),
                'fechaFin' => $request->input('fechaFin'),
                'idOperador' => $request->input('chofer'),
            ]);
            // Recuperar el operador relacionado con la incapacidad
            $operador = operador::find($request->input('chofer'));
            if ($operador) {
                // Actualizar el estado del operador a 'incapacidad' (idEstado = 3)
                $operador->update(['idEstado' => 3]);
                $nombreCompleto = $operador->nombre_completo;
            } else {
                $nombreCompleto = 'desconocido';
            }    
            // Redirigir con un mensaje de éxito que incluye el nombre completo del operador
            return redirect()->route('rh.incapacidades')->with([
                'message' => 'Incapacidad registrado correctamente del operador: ' . $nombreCompleto,
                'color' => 'green',
                'type' => 'success'
            ]);
        }catch(Exception $e){
            return redirect()->route('rh.incapacidades')->with(['message' => "Error al agregar la incapacidad", "color" => "red", 'type' => 'error']);
        }
    }

    public function actualizarIncapacidad(Request $request, $idIncapacidad)
    {
        try {
            // Validar los campos del request
            $request->validate([
                'motivo' => 'required',
                'numeroDias' => 'required',
                'fechaInicio' => 'required',
                'fechaFin' => 'required',
            ]);
            // Buscar la incapacidad por su id
            $incapacidad = Incapacidad::find($idIncapacidad);
            // Verificar si la incapacidad existe
            if (!$incapacidad) {
                return redirect()->route('rh.incapacidades')->with(['message' => "Incapacidad no encontrada", "color" => "red"]);
            }
            // Actualizar los campos de la incapacidad
            $incapacidad->motivo = $request->motivo;
            $incapacidad->numeroDias = $request->numeroDias;
            $incapacidad->fechaInicio = $request->fechaInicio;
            $incapacidad->fechaFin = $request->fechaFin;
            // Guardar los cambios
            $incapacidad->save();

            // Crear el mensaje con la información actualizada
            $message = "Incapacidad actualizada correctamente. Motivo: " . $incapacidad->motivo . 
            ", Número de días: " . $incapacidad->numeroDias . 
            ", Fecha de inicio: " . $incapacidad->fechaInicio . 
            ", Fecha de fin: " . $incapacidad->fechaFin;
    
            return redirect()->route('rh.incapacidades')->with(['message' => $message, "color" => "green"]);
        } catch (Exception $e) {
            // Capturar cualquier excepción y redirigir con un mensaje de error
            return redirect()->route('rh.incapacidades')->with(['message' => "Error al actualizar la incapacidad", "color" => "red"]);
        }
    }

    public function eliminarIncapacidad($incapacidadesIds){
        try{
            // Convierte la cadena de IDs en un array
            $incapacidadesIdsArray = explode(',', $incapacidadesIds);
            // Limpia los IDs para evitar posibles problemas de seguridad
            $incapacidadesIdsArray = array_map('intval', $incapacidadesIdsArray);
            // Elimina las materias
            incapacidad::whereIn('idIncapacidad', $incapacidadesIdsArray)->delete();
            // Redirige a la página deseada después de la eliminación
            return redirect()->route('rh.incapacidades')->with(['message' => "Incapacidad eliminada correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('rh.incapacidades')->with(['message' => "No se pudo eliminar la incapacidad", "color" => "red"]);
        }
    }

    public function reincorporarOperador(Request $request){
        try {
            // Validar la solicitud
            $request->validate([
                'chofer' => 'required|exists:operador,idOperador', // Verifica que el operador exista
            ]);
            // Recuperar el operador
            $operador = operador::find($request->input('chofer'));
            if ($operador) {
                // Actualizar el estado del operador a 'Alta' (idEstado = 1)
                $operador->update(['idEstado' => 1]);
                // Recuperar el nombre completo del operador
                $nombreCompleto = $operador->nombre_completo;
                // Redirigir con un mensaje de éxito que incluye el nombre completo del operador
                return redirect()->route('rh.incapacidades')->with(['message' => 'Operador ' . $nombreCompleto . ' reincorporado correctamente','color' => 'green','type' => 'success'
                ]);
            } else {
                // Si el operador no se encuentra
                return redirect()->route('rh.incapacidades')->with(['message' => 'Operador no encontrado','color' => 'red','type' => 'error'
                ]);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y redirigir con un mensaje de error
            return redirect()->route('rh.incapacidades')->with(['message' => 'Error al reincorporar operador','color' => 'red','type' => 'error'
            ]);
        }   
    }

    public function personalAdministrativo(){
        $incapacidad = incapacidad::all();
        $operador = operador::all();
        $operadoresAlta = operador::where('idEstado', 1)->get();
        $operadoresIncapacidad = operador::where('idEstado', 3)->get();
        $usuario = $this->obtenerInfoUsuario();
        return Inertia::render('RH/PersonalAdministrativo',[
            'incapacidad' => $incapacidad,
            'usuario' => $usuario,
            'operador' => $operador,
            'operadoresAlta' => $operadoresAlta,
            'operadoresIncapacidad' => $operadoresIncapacidad,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }
}
