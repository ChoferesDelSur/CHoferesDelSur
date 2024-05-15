<?php

namespace App\Http\Controllers;

use App\Models\unidad;
use App\Models\directivo;
use App\Models\operador;
use App\Models\calificacion;
use App\Models\estado;
use App\Models\formacionunidades;
use App\Models\ruta;
use App\Models\tipodirectivo;
use App\Models\tipooperador;
use App\Models\castigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class PrincipalController extends Controller
{
    /* public function index()
    {
        return Inertia::render('Principal');
    } */
    public function inicio()
    {
        $directivo = directivo::all();
        $operador = operador::all(); 
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        $unidad = unidad::all();
        $ruta = ruta::all();
        return Inertia::render('Principal/Inicio',[
            'unidad' => $unidad,
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'estado' => $estado,
            'ruta' => $ruta
        ]);
    }
    public function formarUnidades(){
        $directivo = directivo::all();
        $unidad = unidad::all();
        $operador = operador::all();
        $ruta = ruta::all();
        $formacionUnidades = formacionunidades::all();
        $castigo = castigo::all();
        return Inertia::render('Principal/FormarUnidades',[
            'directivo' => $directivo,
            'unidad' => $unidad,
            'operador' => $operador,
            'ruta' => $ruta,
            'castigo' => $castigo,
            'formacionUnidades' => $formacionUnidades,
        ]);
    }

    
public function registrarHoraEntrada(Request $request)
{ 
    try{
    // Obtener el ID de la unidad y la hora de entrada del formulario
    $unidadId = $request->input('unidad');
    $horaEntrada = Carbon::parse($request->input('horaEntrada'))->format('H:i'); // Formatear la hora
    $extremo = $request->input('extremo');

    // Obtener el día de la semana
    $fecha = Carbon::now();
    $diaSemana = $fecha->dayOfWeek;

    // Definir los límites de tiempo según el día de la semana y el valor de extremo
    if ($diaSemana === 6 && $extremo === 'si') { // Sábado y extremo es 'si'
        $limiteNormal = Carbon::createFromTime(6, 45);
        $limiteMulta = Carbon::createFromTime(7, 0);//Quizá se quite porque no se considera multa
    } elseif ($diaSemana === 6) { // Sábado (sin considerar extremo)
        $limiteNormal = Carbon::createFromTime(6, 30);
        $limiteMulta = Carbon::createFromTime(7, 0);
    } elseif ($diaSemana === 0) { // Domingo
        $limiteNormal = Carbon::createFromTime(7, 30);
        $limiteMulta = Carbon::createFromTime(7, 45);
    } else { // Lunes a viernes
        $limiteNormal = Carbon::createFromTime(6, 15);
        $limiteMulta = Carbon::createFromTime(6, 30);
    }

    // Convertir la hora de entrada a un objeto Carbon
    $horaEntradaCarbon = Carbon::createFromFormat('H:i', $horaEntrada);

    // Determinar el tipo de entrada
    if ($horaEntradaCarbon < $limiteNormal) {
        $tipoEntrada = 'Normal';
    } elseif ($horaEntradaCarbon >= $limiteNormal && $horaEntradaCarbon <= $limiteMulta) {
        $tipoEntrada = 'Multa';
    } else {
        $tipoEntrada = '';
    }

    // Verificar si la unidad tiene un operador asignado
    $unidad = unidad::find($unidadId);
    if (!$unidad->operador) {
        // La unidad no tiene un operador asignado, puedes manejar el error aquí
        return redirect()->route('principal.formarUni')->with(['message' => "La unidad no tiene operador asignado", "color" => "red"]);
    }

    // Buscar si ya existe un registro para esta unidad
    $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();

    // Si ya existe un registro para esta unidad, actualizar la hora de entrada y el tipo de entrada
    if ($formacionUnidad) {
        $formacionUnidad->horaEntrada = $horaEntrada;
        $formacionUnidad->tipoEntrada = $tipoEntrada;
        $formacionUnidad->extremo = $extremo; // Actualizar el valor extremo
        $formacionUnidad->save();
    } else { // Si no existe un registro para esta unidad, crear uno nuevo
        formacionunidades::create([
            'idUnidad' => $unidadId,
            'horaEntrada' => $horaEntrada,
            'tipoEntrada' => $tipoEntrada,
            'extremo' => $extremo // Guardar el valor extremo
        ]);
    }

    // Devolver una respuesta de éxito
    return redirect()->route('principal.formarUni')->with(['message' => "Hora de entrada registrada correctamente: " . $horaEntrada, "color" => "green"]);
}catch(Exception $e){
    dd($e);
}
}

    public function registrarCorte(Request $request)
    {
        // Validar los datos recibidos del formulario
        $request->validate([
            'unidad' => 'required',
            'horaCorte' => 'required',
            'causa' => 'required',
        ]);

        try {
            // Obtener el ID de la unidad seleccionada del formulario
            $unidadId = $request->input('unidad');

            // Buscar si ya existe un registro para esta unidad
            $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();

            // Verificar si la unidad tiene registrada la hora de entrada
            if (!$formacionUnidad || !$formacionUnidad->horaEntrada) {
                return redirect()->route('principal.formarUni')->with(['message' => "No se puede registrar la hora de corte porque la unidad no tiene registrada la hora de entrada.", "color" => "red"]);
            }

            // Si ya existe un registro para esta unidad, actualizar la hora de corte, causa y hora de regreso
            if ($formacionUnidad) {
                $formacionUnidad->horaCorte = $request->input('horaCorte');
                $formacionUnidad->causa = $request->input('causa');
                $formacionUnidad->horaRegreso = $request->input('horaRegreso');
                // Otros campos necesarios...

                $formacionUnidad->save();
            } else { // Si no existe un registro para esta unidad, crear uno nuevo
                formacionunidades::create([
                    'idUnidad' => $unidadId,
                    'horaCorte' => $request->input('horaCorte'),
                    'causa' => $request->input('causa'),
                    'horaRegreso' => $request->input('horaRegreso'),
                    // Otros campos necesarios...
                ]);
            }

            // Aquí puedes realizar otras acciones si es necesario, como enviar una respuesta JSON de éxito, etc.
            return redirect()->route('principal.formarUni')->with(['message' => "Hora de corte registrada correctamente: ", "color" => "green"]);
        } catch (Exception $e) {
            // Manejar cualquier error que pueda ocurrir durante la operación
            return redirect()->route('principal.formarUni')->with(['message' => "Error: " . $e->getMessage(), "color" => "red"]);
        }
    }

    public function registrarCastigo(Request $request){
        // Validar los datos recibidos del formulario
        
        $request->validate([
            'unidad' => 'required',
            'castigo' => 'required',
            'horaInicio' => 'required',
            'horaFin' => 'required',
            /* 'observaciones' => 'required', */
        ]);
        try{
            // Obtener el ID de la unidad seleccionada del formulario
            $unidadId = $request->input('unidad');
            
            // Buscar si ya existe un registro para esta unidad
            $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();

            // Verificar si la unidad tiene registrada la hora de entrada
            if (!$formacionUnidad || !$formacionUnidad->horaEntrada) {
                return redirect()->route('principal.formarUni')->with(['message' => "No se puede registrar la hora de corte porque la unidad no tiene registrada la hora de entrada.", "color" => "red"]);
            }

            // Crear una nueva instancia del modelo castigo
            $nuevoCastigo = new castigo();
            
            // Asignar los valores a los atributos del modelo
            $nuevoCastigo->idUnidad = $unidadId;
            $nuevoCastigo->castigo = $request->input('castigo');
            $nuevoCastigo->horaInicio = $request->input('horaInicio');
            $nuevoCastigo->horaFin = $request->input('horaFin');
            $nuevoCastigo->observaciones = $request->input('observaciones');
            
            // Guardar el nuevo castigo en la base de datos
            $nuevoCastigo->save();

            return redirect()->route('principal.formarUni')->with(['message' => "Castigo registrado correctamente: ", "color" => "green"]);
        }catch(Exception $e){
            dd("Error: " + $e);
            return redirect()->route('principal.formarUni')->with(['message' => "Error: " . $e->getMessage(), "color" => "red"]);
        }
    }

    public function RegistrarHoraRegreso(Request $request){
        $request->validate([
            'unidad' => 'required',
            'horaRegreso' => 'required',
        ]);
    
        try{
            $unidadId = $request->input('unidad');
    
            // Buscar si ya existe un registro para esta unidad
            $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();
    
            if ($formacionUnidad) {
                // Verificar si la unidad tiene horaCorte registrada
                if (!$formacionUnidad->horaCorte) {
                    return redirect()->route('principal.formarUni')->with(['message' => "La unidad no tiene registrada la hora de corte", "color" => "red"]);
                }
    
                // Verificar que la hora de regreso sea mayor o igual a la hora de corte
                $horaCorte = \Carbon\Carbon::parse($formacionUnidad->horaCorte);
                $horaRegreso = \Carbon\Carbon::parse($request->input('horaRegreso'));
                if ($horaRegreso->lessThan($horaCorte)) {
                    return redirect()->route('principal.formarUni')->with(['message' => "La hora de regreso no puede ser menor a la hora de corte", "color" => "red"]);
                }
    
                // Actualizar la hora de regreso
                $formacionUnidad->horaRegreso = $request->input('horaRegreso');
                $formacionUnidad->save();
            } else {
                // Si no existe un registro para esta unidad, crear uno nuevo
                formacionunidades::create([
                    'idUnidad' => $unidadId,
                    'horaRegreso' => $request->input('horaRegreso'),
                ]);
            }
    
            return redirect()->route('principal.formarUni')->with(['message' => "Hora de regreso registrado correctamente", "color" => "green"]);
        } catch(Exception $e){
            return redirect()->route('principal.formarUni')->with(['message' => "Error al registrar la hora de regreso", "color" => "red"]);
        }
    }

    public function registrarUC(Request $request){
        $request->validate([
            'unidad' => 'required',
            'horaInicioUC' => 'required',
        ]);
        try{
            // Obtener el ID de la unidad seleccionada del formulario
            $unidadId = $request->input('unidad');

            // Buscar si ya existe un registro para esta unidad
            $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();

            // Verificar si la unidad tiene registrada la hora de entrada
            if (!$formacionUnidad || !$formacionUnidad->horaEntrada) {
                return redirect()->route('principal.formarUni')->with(['message' => "No se puede registrar la hora de inicio de la UC porque la unidad no formó.", "color" => "red"]);
            }

            // Si ya existe un registro para esta unidad, actualizar la hora de corte, causa y hora de regreso
            if ($formacionUnidad) {
                $formacionUnidad->horaInicioUC = $request->input('horaInicioUC');
                $formacionUnidad->ultimaCorrida = 'SI';
                $formacionUnidad->horaFinUC = $request->input('horaFinUC');

                $formacionUnidad->save();
            } else { // Si no existe un registro para esta unidad, crear uno nuevo
                formacionunidades::create([
                    'idUnidad' => $unidadId,
                    'horaInicioUC' => $request->input('horaInicioUC'),
                    'ultimaCorrida' => 'SI',
                    'horaFinUC' => $request->input('horaFinUC'),
                    // Otros campos necesarios...
                ]);
            }
            return redirect()->route('principal.formarUni')->with(['message' => "Última corrida de la unidad registrado correctamente ", "color" => "green"]);

        }catch(Exception $e){
            return redirect()->route('principal.formarUni')->with(['message' => "Error al registrar la última corrida de la unidad", "color" => "red"]);
        }
    }

    public function RegistrarHoraRegresoUC(Request $request){
        $request->validate([
            'unidad' => 'required',
            'horaFinUC' => 'required',
        ]);
    
        try{
            $unidadId = $request->input('unidad');
    
            // Buscar si ya existe un registro para esta unidad
            $formacionUnidad = formacionunidades::where('idUnidad', $unidadId)->first();
    
            if ($formacionUnidad) {
                // Verificar si la unidad tiene horaCorte registrada
                if (!$formacionUnidad->horaInicioUC) {
                    return redirect()->route('principal.formarUni')->with(['message' => "La unidad no tiene registrada la hora de inicio de la UC", "color" => "red"]);
                }
    
                // Verificar que la hora de regreso sea mayor o igual a la hora de corte
                $horaInicioUC = \Carbon\Carbon::parse($formacionUnidad->horaInicioUC);
                $horaFinUC = \Carbon\Carbon::parse($request->input('horaFinUC'));
                if ($horaFinUC->lessThan($horaInicioUC)) {
                    return redirect()->route('principal.formarUni')->with(['message' => "La hora de regreso no puede ser menor a la hora de inicio de la UC", "color" => "red"]);
                }
    
                // Actualizar la hora de regreso
                $formacionUnidad->horaFinUC = $request->input('horaFinUC');
                $formacionUnidad->save();
            } else {
                // Si no existe un registro para esta unidad, crear uno nuevo
                formacionunidades::create([
                    'idUnidad' => $unidadId,
                    'horaFinUC' => $request->input('horaFinUC'),
                ]);
            }
    
            return redirect()->route('principal.formarUni')->with(['message' => "Hora de regreso de la UC registrado correctamente", "color" => "green"]);
        } catch(Exception $e){
            return redirect()->route('principal.formarUni')->with(['message' => "Error al registrar la hora de regreso de la UC", "color" => "red"]);
        }
    }

    public function registrarTrabajanDomingo(Request $request)
    {
        try {
            // Obtener los datos del formulario
            $datosFormulario = $request->validate([
                'unidadesSeleccionadas' => 'required|array', // Array con las IDs de las unidades seleccionadas
            ]);
    
            // Actualizar las unidades seleccionadas a 'NO'
            formacionunidades::whereIn('idUnidad', $datosFormulario['unidadesSeleccionadas'])
                ->update(['trabajaDomingo' => 'NO']);
    
            // Obtener todas las unidades que NO se han seleccionado
            $unidadesNoSeleccionadas = formacionunidades::whereNotIn('idUnidad', $datosFormulario['unidadesSeleccionadas'])
                ->get();
    
            // Asignar 'NO' a la columna trabajaDomingo de las unidades NO seleccionadas
            foreach ($unidadesNoSeleccionadas as $unidad) {
                $unidad->update(['trabajaDomingo' => 'SI']);
            }
    
            // Redireccionar con un mensaje de éxito
            return redirect()->route('principal.formarUni')->with(['message' => "Unidades registradas correctamente", "color" => "green"]);
        } catch (Exception $e) {
            // Manejo de excepciones
            return redirect()->route('principal.formarUni')->with(['message' => "Error: " . $e->getMessage(), "color" => "red"]);
        }
    } 

    public function unidades(){
        $unidad = unidad::all();
        $operador = operador::all(); 
        $directivo = directivo::all();
        
        // Obtener los operadores disponibles
        $operadoresDisp = operador::where('idEstado', 1) // Filtrar por estado "Alta"
                                   ->whereDoesntHave('unidad') // Verificar que no estén relacionados con ninguna unidad
                                   ->get();
        
        // Obtener las unidades disponibles (sin operador asignado)
        $unidadesDisp = unidad::whereNull('idOperador')->get();
        
        $ruta = ruta::all();
        
        return Inertia::render('Principal/Unidades', [
            'unidad' => $unidad,
            'operador' => $operador,
            'operadoresDisp' => $operadoresDisp,
            'unidadesDisp' => $unidadesDisp, // Pasar las unidades disponibles a la vista
            'ruta' => $ruta,
            'directivo' => $directivo,
        ]);
    }
    

    public function addUnidad(Request $request){
        try{
            $request->validate([
                'numeroUnidad'=> 'required',
                'ruta' => 'required',
                'directivo' => 'required',
            ]);
    
            // Verificar si la unidad ya existe
            $existingUnidad = unidad::where('numeroUnidad', $request->numeroUnidad)
                ->where('idRuta', $request->ruta)
                ->where('idDirectivo', $request->directivo)
                ->first();
    
            if($existingUnidad){
                // Unidad ya existe, puedes devolver una respuesta indicando el error
                return redirect()->route('principal.unidades')->with(['message' => "La unidad ya existe.", "color" => "red"]);
            }
        
            $unidad = new unidad();
            $unidad->numeroUnidad = $request->numeroUnidad;
            $unidad->idRuta = $request->ruta;
            $unidad->idDirectivo = $request->directivo;
    
            // Verifica si se proporcionó un operador antes de asignarlo a la unidad
            if ($request->has('operador')) {
                $unidad->idOperador = $request->operador;
            }
            
            $unidad->save();
    
            // Ahora, registra la misma unidad en la tabla "formacionUnidades"
            $formacionUnidad = new formacionunidades();
            $formacionUnidad->idUnidad = $unidad->idUnidad; // Utilizamos el idUnidad de la unidad recién creada
            $formacionUnidad->save();
            
            return redirect()->route('principal.unidades')->with(['message' => "Unidad agregada correctamente:" . $request->numeroUnidad, "color" => "green"]);
        } catch(Exception $e){
            return redirect()->route('principal.unidades');
        }
    }

    public function actualizarUnidad(Request $request, $idUnidad)
    {
        try{
            $request->validate([
                'numeroUnidad'=> 'required',
                'ruta' => 'required',
                'operador' => 'required',
            ]);
            $unidad = unidad::find($idUnidad);
            $unidad->numeroUnidad = $request -> numeroUnidad;
            $unidad->idOperador = $request->operador;
            $unidad->idRuta = $request->ruta;
            $unidad->save();

            return redirect()->route('principal.unidades')->with(['message' => "Unidad actualizado correctamente: " . $request->numeroUnidad, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.unidades')->with(['message' => "La unidad no se actualizó correctamente: " . $requests->numeroUnidad, "color" => "reed"]);
        }
    }

    public function eliminarUnidad($unidadesIds){
        try{
            // Convierte la cadena de IDs en un array
            $unidadesIdsArray = explode(',', $unidadesIds);

            // Limpia los IDs para evitar posibles problemas de seguridad
            $unidadesIdsArray = array_map('intval', $unidadesIdsArray);

            // Elimina las materias
            unidad::whereIn('idUnidad', $unidadesIdsArray)->delete();
            // Redirige a la página deseada después de la eliminación
            return redirect()->route('principal.unidades')->with(['message' => "Unidad eliminado correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.unidades')->with(['message' => "No se pudo eliminar la unidad", "color" => "red"]);
        }
    }

    public function asignarOperador(Request $request)
    {
        // Obtener los IDs de la unidad y el operador del request
        $unidadId = $request->input('unidad');
        $operadorId = $request->input('operador');
        
        // Buscar la unidad y el operador en la base de datos
        $unidad = Unidad::findOrFail($unidadId);
        $operador = Operador::findOrFail($operadorId);

        // Asignar el operador a la unidad
        $unidad->idOperador = $operadorId; // Suponiendo que tienes una columna 'operador_id' en tu tabla de unidades
        $unidad->save();

        // Puedes retornar algún mensaje de éxito si lo deseas
        return redirect()->route('principal.unidades')->with(['message' => 'Operador asignado correctamente a la unidad.']);
    }

    public function operadores(){
        $operador = operador::all(); 
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        $directivo = directivo::all();
        return Inertia::render('Principal/Operadores',[
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'estado' => $estado,
            'directivo' => $directivo,
        ]);
    }

    public function addOperador(Request $request){
        try{
            $request->validate([
                'nombre'=> 'required',
                'apellidoP'=> 'required',
                'apellidoM' => 'required',
                'tipoOperador' => 'required',
                'estado' => 'required',
                'directivo' => 'required',
            ]);
            // Verificar si el operador ya existe
            $existingOperador = Operador::where('nombre', $request->nombre)
            ->where('apellidoP', $request->apellidoP)
            ->where('apellidoM', $request->apellidoM)
            ->first();

            if($existingOperador){
            // Operador ya existe, puedes devolver una respuesta indicando el error
            return redirect()->route('principal.operadores')->with(['message' => "El operador ya existe.", "color" => "red"]);
            }
    
            $operador = new operador();
            $operador->nombre = $request->nombre;
            $operador->apellidoP = $request->apellidoP;
            $operador->apellidoM = $request->apellidoM;
            $operador->idTipoOperador = $request->tipoOperador;
            $operador->idEstado = $request->estado;
            $operador->idDirectivo = $request->directivo;

            $nombreCompleto = $operador->apellidoP . ' ' . $operador->apellidoM . ' ' . $operador->nombre;
            $operador->nombre_completo = $nombreCompleto;

            $operador->save();
            return redirect()->route('principal.operadores')->with(['message' => "Operador agregado correctamente: $nombreCompleto", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.operadores');
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
            $operador->save();

            return redirect()->route('principal.operadores')->with(['message' => "Operador actualizado correctamente: " . $request->nombre, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.operadores')->with(['message' => "El operador no se actualizó correctamente: " . $requests->nombre, "color" => "reed"]);
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
            return redirect()->route('principal.operadores')->with(['message' => "Operador eliminado correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.operadores')->with(['message' => "No se pudo eliminar al operador", "color" => "red"]);
        }
    }

    public function sociosPrestadores(){
        $directivo = directivo::all();
        $operador = operador::all();
        $tipDirectivo = tipodirectivo::all();
        return Inertia::render('Principal/SociosPrestadores',[
            'directivo' => $directivo,
            'operador' => $operador,
            'tipDirectivo' => $tipDirectivo,
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
                return redirect()->route('principal.sociosPrestadores')->with(['message' => "El directivo $nombreCompleto ya existe.", "color" => "red"]);
            }
    
            $directivo = new directivo();
            $directivo->nombre = $request->nombre;
            $directivo->apellidoP = $request->apellidoP;
            $directivo->apellidoM = $request->apellidoM;
            $directivo->idTipoDirectivo = $request->tipDirectivo;
            $nombreCompleto =$directivo->apellidoP . ' ' . $directivo->apellidoM. ' ' . $directivo->nombre;
            $directivo->nombre_completo = $nombreCompleto;

            $directivo->save();
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "Operador agregado correctamente: $nombreCompleto", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.sociosPrestadores');
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

            return redirect()->route('principal.sociosPrestadores')->with(['message' => "Directivo actualizado correctamente: " . $nombreCompleto, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "El directivo no se actualizó correctamente: " . $nombreCompleto, "color" => "reed"]);
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
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "Directivo eliminada correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "No se pudo eliminar al directivo", "color" => "red"]);
        }
    }

    public function rutas(){
        $ruta = ruta::all();
        return Inertia::render('Principal/Rutas',[
            'ruta' => $ruta,
        ]);
    }

    public function addRuta(Request $request){
        try{
            $request->validate([
                'nombreRuta'=> 'required',
            ]);

             // Verificar si la ruta ya existe en la base de datos
            $existingRuta = ruta::where('nombreRuta', $request->nombreRuta)->first();

            if ($existingRuta) {
                // Si ya existe, maneja la situación como desees, por ejemplo, redirigir con un mensaje de error.
                return redirect()->route('principal.rutas')->with(['message' => "La ruta ya está registrada: " . $request->nombreRuta, "color" => "red"]);
            }
    
            $ruta = new ruta();
            $ruta->nombreRuta = $request->nombreRuta;
            $ruta->save();

            return redirect()->route('principal.rutas')->with(['message' => "Ruta agregado correctamente: . $request->nombreRuta ", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.rutas');
        }
    }

    public function actualizarRuta(Request $request, $idRuta)
    {
        try{
            $request->validate([
                'nombreRuta' => 'required',
            ]);
            $ruta = ruta::find($idRuta);
            $ruta->nombreRuta = $request->nombreRuta;
            $ruta->save();

            return redirect()->route('principal.rutas')->with(['message' => "Ruta actualizada correctamente: " . $request->nombreRuta, "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.rutas')->with(['message' => "La ruta no se actualizó correctamente: " . $request->nombreRuta, "color" => "reed"]);
        }
    }

    public function eliminarRuta($rutasIds){
        try{
            // Convierte la cadena de IDs en un array
            $rutasIdsArray = explode(',', $rutasIds);

            // Limpia los IDs para evitar posibles problemas de seguridad
            $rutasIdsArray = array_map('intval', $rutasIdsArray);

            // Elimina las materias
            ruta::whereIn('idRuta', $rutasIdsArray)->delete();
            // Redirige a la página deseada después de la eliminación
            return redirect()->route('principal.rutas')->with(['message' => "Ruta eliminada correctamente", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.rutas')->with(['message' => "No se pudo eliminar la ruta", "color" => "red"]);
        }
    }

}