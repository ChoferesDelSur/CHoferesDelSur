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
use App\Models\corte;
use App\Models\entrada;
use App\Models\rolServicio;
use App\Models\ultimaCorrida;
use App\Models\tipoUltimaCorrida;
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
            'ruta' => $ruta,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }
    public function formarUnidades(){
        $directivo = directivo::all();
        $unidad = unidad::all();
        $operador = operador::all();
        $ruta = ruta::all();
        $formacionUnidades = formacionunidades::all();
        $castigo = castigo::all();
        $corte = corte::all();
        $entrada = entrada::all();
        $rolServicio = rolServicio::all();
        $ultimaCorrida = ultimaCorrida::all();
        $tipoUltimaCorrida = tipoUltimaCorrida::all();
        return Inertia::render('Principal/FormarUnidades',[
            'directivo' => $directivo,
            'unidad' => $unidad,
            'operador' => $operador,
            'ruta' => $ruta,
            'castigo' => $castigo,
            'formacionUnidades' => $formacionUnidades,
            'corte' => $corte,
            'entrada' => $entrada,
            'rolServicio' => $rolServicio,
            'ultimaCorrida' => $ultimaCorrida,
            'tipoUltimaCorrida' => $tipoUltimaCorrida,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

    
    public function registrarHoraEntrada(Request $request)
    {
        try {
            // Obtener el ID de la unidad y la hora de entrada del formulario
            $unidadId = $request->input('unidad');
            $horaEntrada = Carbon::parse($request->input('horaEntrada'))->format('H:i'); // Formatear la hora
            $extremo = $request->input('extremo');
    
            // Verificar si la unidad existe
            $unidad = Unidad::find($unidadId);
            if (!$unidad) {
                // La unidad no existe, puedes manejar el error aquí
                return redirect()->back()->with(['message' => "La unidad no existe", "color" => "yellow", 'type' => 'info']);
            }
    
            // Verificar si la unidad tiene un operador asignado
            if (!$unidad->operador) {
                // La unidad no tiene un operador asignado, puedes manejar el error aquí
                return redirect()->back()->with(['message' => "La unidad {$unidad->numeroUnidad} no tiene operador asignado", "color" => "yellow", 'type' => 'info']);
            }
    
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
    
            // Verificar si ya existe una entrada para esta unidad en el día actual
            $entradaExistente = Entrada::where('idUnidad', $unidadId)
            ->whereDate('created_at', Carbon::today())
            ->first();

            if ($entradaExistente) {
                return redirect()->back()->with(['message' => "Ya está registrado la hora de entrada para la unidad {$unidad->numeroUnidad} el día de hoy", "color" => "yellow", 'type' => 'info']);
            }

            // Crear un nuevo registro en la tabla de entrada
            Entrada::create([
                'idUnidad' => $unidadId,
                'horaEntrada' => $horaEntrada,
                'tipoEntrada' => $tipoEntrada,
                'extremo' => $extremo,
            ]);
    
            return redirect()->back()->with(['message' => "Hora de entrada {$horaEntrada} registrada correctamente para la unidad {$unidad->numeroUnidad}", "color" => "green", 'type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['message' => "Error al registrar la hora de entrada", "color" => "red", 'type' => 'error']);
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
        $unidad = Unidad::find($unidadId);

        $numeroUnidad = $unidad->numeroUnidad;

        // Verificar si la unidad tiene registrada la hora de entrada en la tabla entradas
        $fechaActual = Carbon::now()->toDateString();

        // Usar created_at para verificar la fecha actual
        $entrada = entrada::where('idUnidad', $unidadId)
                          ->whereDate('created_at', $fechaActual)
                          ->first();

        if (!$entrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene registrada la hora de entrada el día de hoy.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }

        // Depuración: Verificar el valor de horaEntrada
        if (!$entrada->horaEntrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene hora de entrada registrada.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }

        // Validar que la horaRegreso no sea menor que la horaCorte, si se proporciona
        $horaCorte = \Carbon\Carbon::parse($request->input('horaCorte'));
        if ($request->has('horaRegreso')) {
            $horaRegreso = \Carbon\Carbon::parse($request->input('horaRegreso'));
            if ($horaRegreso->lessThan($horaCorte)) {
                $horaCorteFormateada = $horaCorte->format('H:i');
                $horaRegresoFormateada = $horaRegreso->format('H:i');
                return redirect()->route('principal.formarUni')->with([
                    'message' => "La hora de regreso {$horaRegresoFormateada} no puede ser menor que la hora de corte {$horaCorteFormateada}.",
                    'color' => 'red',
                    'type' => 'error'
                ]);
            }
        }

        // Crear un nuevo registro de corte
        Corte::create([
            'idUnidad' => $unidadId,
            'horaCorte' => $request->input('horaCorte'),
            'causa' => $request->input('causa'),
            'horaRegreso' => $request->input('horaRegreso'),
            // Otros campos necesarios...
        ]);

        // Aquí puedes realizar otras acciones si es necesario, como enviar una respuesta JSON de éxito, etc.
        return redirect()->route('principal.formarUni')->with([
            'message' => "Hora de corte " . $request->horaCorte . " registrada correctamente para la unidad " . $numeroUnidad . " por " . $request->causa,
            "color" => "green",
            'type' => 'success'
        ]);
    } catch (Exception $e) {
        // Manejar cualquier error que pueda ocurrir durante la operación
        return redirect()->route('principal.formarUni')->with([
            'message' => "Error: " . $e->getMessage(),
            "color" => "red",
            'type' => 'error'
        ]);
    }
    }

    public function registrarCastigo(Request $request){
         // Validar los datos recibidos del formulario
    $validatedData = $request->validate([
        'unidad' => 'required|exists:unidad,idUnidad', // Asegúrate de que la unidad existe
        'castigo' => 'required|string|max:255',
        'horaInicio' => 'required',
        'horaFin' => 'required', // Asegúrate de que la horaFin sea posterior a la horaInicio
        'observaciones' => 'nullable|string|max:1000', // Observaciones opcionales
    ]);

    try {
        // Obtener el ID de la unidad seleccionada del formulario
        $unidadId = $validatedData['unidad'];
        $unidad = Unidad::findOrFail($unidadId);
        $numeroUnidad = $unidad->numeroUnidad;

        // Verificar si la unidad tiene registrada la hora de entrada en la tabla entradas
        $fechaActual = Carbon::now()->toDateString();

        // Usar created_at para verificar la fecha actual
        $entrada = Entrada::where('idUnidad', $unidadId)
                          ->whereDate('created_at', $fechaActual)
                          ->first();

        if (!$entrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene registrada la hora de entrada el día de hoy.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }

        // Depuración: Verificar el valor de horaEntrada
        if (!$entrada->horaEntrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene hora de entrada registrada.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }

        // Obtener la hora de inicio y la hora de fin del request
        $horaInicio = \Carbon\Carbon::parse($validatedData['horaInicio']);
        $horaFin = \Carbon\Carbon::parse($validatedData['horaFin']);

        // Formatear las horas para mostrar solo horas y minutos
        $horaInicioFormateada = $horaInicio->format('H:i');
        $horaFinFormateada = $horaFin->format('H:i');
        if ($horaFin->lessThanOrEqualTo($horaInicio)) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La hora  de finalización del castigo " . $horaFinFormateada . " no puede ser igual o menor que la hora de inicio del castigo " .$horaInicioFormateada,
                'color' => 'red',
                'type' => 'error'
            ]);
        }

        // Crear una nueva instancia del modelo castigo
        $nuevoCastigo = new Castigo();
        $nuevoCastigo->idUnidad = $unidadId;
        $nuevoCastigo->castigo = $validatedData['castigo'];
        $nuevoCastigo->horaInicio = $validatedData['horaInicio'];
        $nuevoCastigo->horaFin = $validatedData['horaFin'];
        $nuevoCastigo->observaciones = $validatedData['observaciones'] ?? '';

        // Guardar el nuevo castigo en la base de datos
        $nuevoCastigo->save();

        return redirect()->route('principal.formarUni')->with([
            'message' => "Castigo registrado correctamente para la unidad " . $numeroUnidad,
            'color' => 'green',
            'type' => 'success'
        ]);
    } catch (Exception $e) {
        return redirect()->route('principal.formarUni')->with([
            'message' => "Error: " . $e->getMessage(),
            'color' => 'red',
            'type' => 'error'
        ]);
    }
    }

    public function RegistrarHoraRegreso(Request $request){
        $request->validate([
            'unidad' => 'required',
            'horaRegreso' => 'required',
        ]);

        try {
            $unidadId = $request->input('unidad');
            $unidad = Unidad::find($unidadId);
            // Obtener el número de unidad para mostrarlo en el mensaje de éxito
            $numeroUnidad = $unidad->numeroUnidad;
    
            // Verificar si la unidad tiene una entrada de corte
            /* $corte = Corte::where('idUnidad', $unidadId)->latest()->first(); */

            // Verificar si la unidad tiene una entrada de corte para hoy
            $corte = Corte::where('idUnidad', $unidadId)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();

            if (!$corte) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad {$numeroUnidad} no tiene registrada hora de corte para hoy.",
                'color' => 'red',
                'type' => 'error'
            ]);
            }
    
            if ($corte) {
                // Verificar que la hora de regreso sea mayor o igual a la hora de corte
                $horaCorte = \Carbon\Carbon::parse($corte->horaCorte);
                $horaRegreso = \Carbon\Carbon::parse($request->input('horaRegreso'));
    
                if ($horaRegreso->lessThan($horaCorte)) {
                    $horaCorteFormateada = $horaCorte->format('H:i'); // Formatear la hora de corte
                    $horaRegresoFormateada = $horaRegreso->format('H:i'); // Formatear la hora de regreso
                    return redirect()->route('principal.formarUni')->with(['message' => "La hora de regreso " . $horaRegresoFormateada . " no puede ser menor que la hora de corte " .$horaCorteFormateada, "color" => "yellow", 'type' => 'info']);
                }
    
                // Actualizar la hora de regreso
                $corte->horaRegreso = $request->input('horaRegreso');
                $corte->save();
            } else {
                // Si no existe un registro de corte para esta unidad, lanzar una excepción
                return redirect()->route('principal.formarUni')->with(['message' => "La unidad " . $numeroUnidad . " no tiene registrado hora de corte", "color" => "red", 'type' => 'error']);
            }
    
            return redirect()->route('principal.formarUni')->with(['message' => "Hora de regreso de la unidad " . $numeroUnidad . " registrado correctamente", "color" => "green", 'type' => 'success']);

        } catch(Exception $e){
            return redirect()->route('principal.formarUni')->with(['message' => "Error al registrar la hora de regreso", "color" => "red", 'type' => 'error']);
        }
    }

    public function registrarUC(Request $request){
        $request->validate([
            'unidad' => 'required',
            'horaInicioUC' => 'required',
            'tipoUltimaCorrida' => 'required',
        ]);
    
        try {
            $unidadId = $request->input('unidad');
            $unidad = Unidad::find($unidadId);
    
            $numeroUnidad = $unidad->numeroUnidad;
    
            // Verificar si la unidad tiene registrada la hora de entrada en la tabla entradas
        $fechaActual = Carbon::now()->toDateString();

        // Usar created_at para verificar la fecha actual
        $entrada = entrada::where('idUnidad', $unidadId)
                          ->whereDate('created_at', $fechaActual)
                          ->first();

        if (!$entrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene registrada la hora de entrada el día de hoy.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }

        // Depuración: Verificar el valor de horaEntrada
        if (!$entrada->horaEntrada) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad . " no tiene hora de entrada registrada.",
                'color' => 'yellow',
                'type' => 'info'
            ]);
        }
    
            // Validar que la horaFinUC no sea menor que la horaInicioUC, si se proporciona
            $horaInicioUC = \Carbon\Carbon::parse($request->input('horaInicioUC'));
            if ($request->has('horaFinUC')) {
                $horaFinUC = \Carbon\Carbon::parse($request->input('horaFinUC'));
                if ($horaFinUC->lessThan($horaInicioUC)) {
                    $horaInicioUCFormateada = $horaInicioUC->format('H:i');
                    $horaFinUCFormateada = $horaFinUC->format('H:i');
                    return redirect()->route('principal.formarUni')->with([
                        'message' => "La hora de regreso $horaFinUCFormateada no puede ser menor que la hora de inicio de la corrida $horaInicioUCFormateada",
                        'color' => 'red',
                        'type' => 'error'
                    ]);
                }
            }
    
            // Crear un nuevo registro en ultimaCorrida
            UltimaCorrida::create([
                'idUnidad' => $unidadId,
                'horaInicioUC' => $request->input('horaInicioUC'),
                'horaFinUC' => $request->input('horaFinUC'),
                'idTipoUltimaCorrida' => $request->input('tipoUltimaCorrida'),
                // Otros campos necesarios...
            ]);
    
            return redirect()->route('principal.formarUni')->with([
                'message' => "Última corrida de la unidad " .$numeroUnidad ." registrada correctamente",
                'color' => 'green',
                'type' => 'success'
            ]);
    
        } catch(Exception $e) {
            return redirect()->route('principal.formarUni')->with([
                'message' => "Error al registrar la última corrida de la unidad",
                'color' => 'red',
                'type' => 'error'
            ]);
        }
    
    }

    public function RegistrarHoraRegresoUC(Request $request){
        // Validar los campos del formulario
    $request->validate([
        'unidad' => 'required',
        'horaFinUC' => 'required',
    ]);

    try {
        $unidadId = $request->input('unidad');
        $unidad = Unidad::find($unidadId);

        if (!$unidad) {
            return redirect()->route('principal.formarUni')->with([
                'message' => 'Unidad no encontrada.',
                'color' => 'red',
                'type' => 'error'
            ]);
        }

        // Obtener el número de unidad para mostrarlo en el mensaje de éxito
        $numeroUnidad = $unidad->numeroUnidad;

        /* // Buscar el último registro para esta unidad en ultimaCorrida
        $ultimaCorrida = UltimaCorrida::where('idUnidad', $unidadId)->orderBy('created_at', 'desc')->first(); */

        // Buscar el último registro de inicio de la UC para esta unidad hoy
        $ultimaCorrida = UltimaCorrida::where('idUnidad', $unidadId)
        ->whereDate('created_at', Carbon::today())
        ->orderBy('created_at', 'desc')
        ->first();

        if (!$ultimaCorrida) {
        return redirect()->route('principal.formarUni')->with([
            'message' => "La unidad {$numeroUnidad} no tiene registrado hora de inicio de BN, UB o UC para hoy.",
            'color' => 'red',
            'type' => 'error'
        ]);
        }

        if ($ultimaCorrida) {
            // Verificar que la hora de regreso sea mayor o igual a la hora de inicio de la UC
            $horaInicioUC = \Carbon\Carbon::parse($ultimaCorrida->horaInicioUC);
            $horaFinUC = \Carbon\Carbon::parse($request->input('horaFinUC'));

            if ($horaFinUC->lessThan($horaInicioUC)) {
                $horaInicioUCFormateada = $horaInicioUC->format('H:i');
                $horaFinUCFormateada = $horaFinUC->format('H:i');
        
                return redirect()->route('principal.formarUni')->with([
                    'message' => "La hora de regreso $horaFinUCFormateada no puede ser menor que la hora de inicio de la corrida $horaInicioUCFormateada",
                    'color' => 'red',
                    'type' => 'error'
                ]);
            }

            // Si ya hay un registro y la horaFinUC es válida, actualizar la horaFinUC
            $ultimaCorrida->update([
                'horaFinUC' => $request->input('horaFinUC'),
            ]);

            return redirect()->route('principal.formarUni')->with([
                'message' => "Hora de regreso de UC de la unidad " . $numeroUnidad . " actualizada correctamente",
                'color' => 'green',
                'type' => 'success'
            ]);
        } else {
            // No se encontró un registro existente para actualizar
            return redirect()->route('principal.formarUni')->with([
                'message' => "La unidad " . $numeroUnidad ." no tiene registrado hora de inicio de BN, UB o UC",
                'color' => 'red',
                'type' => 'error'
            ]);
        }
    } catch (Exception $e) {
        return redirect()->route('principal.formarUni')->with([
            'message' => "Error al registrar la hora de regreso de la UC",
            'color' => 'red',
            'type' => 'error'
        ]);
    }
    }

    public function registrarTrabajanDomingo(Request $request)
    {
        try {
            $unidadesSi = $request->input('unidadesSi', []);
            $unidadesNo = $request->input('unidadesNo', []);
    
            if (empty($unidadesSi) && empty($unidadesNo)) {
                throw new \Exception("No se han seleccionado unidades.");
            }
    
            \DB::transaction(function () use ($unidadesSi, $unidadesNo) {
                // Actualizar registros existentes en la tabla rolServicio para unidades que trabajan el domingo (SI)
                foreach ($unidadesSi as $unidadId) {
                    $rolServicio = rolServicio::where('idUnidad', $unidadId)->first();
                    if ($rolServicio) {
                        $rolServicio->update(['trabajaDomingo' => 'SI']);
                    } else {
                        rolServicio::create(['idUnidad' => $unidadId, 'trabajaDomingo' => 'SI']);
                    }
                }
            
                // Actualizar registros existentes en la tabla rolServicio para unidades que no trabajan el domingo (NO)
                foreach ($unidadesNo as $unidadId) {
                    $rolServicio = rolServicio::where('idUnidad', $unidadId)->first();
                    if ($rolServicio) {
                        $rolServicio->update(['trabajaDomingo' => 'NO']);
                    } else {
                        rolServicio::create(['idUnidad' => $unidadId, 'trabajaDomingo' => 'NO']);
                    }
                }
            });

            // Si no se lanzó ninguna excepción, la transacción se confirmará automáticamente
    
            return redirect()->route('principal.formarUni')->with([
                'message' => "Se han actualizado correctamente las unidades que trabajarán y no trabajarán el domingo.",
                'color' => 'green'
            ]);
        } catch (\Exception $e) {
            // Cualquier excepción lanzada dentro de la transacción hará que se reviertan automáticamente los cambios
    
            return redirect()->route('principal.formarUni')->with([
                'message' => "Error: " . $e->getMessage(),
                'color' => 'red'
            ]);
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
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
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

        // Obtener el nombre completo del directivo y el nombre de la ruta
        $nombredirectivo = directivo::find($request->directivo)->nombre_completo;
        $nombreruta = ruta::find($request->ruta)->nombreRuta;
    
            if($existingUnidad){
                // Unidad ya existe, puedes devolver una respuesta indicando el error
                return redirect()->route('principal.unidades')->with(['message' => "La unidad ya está registrada: " .$request->numeroUnidad ." - " .$nombreruta ." - " .$nombredirectivo, "color" => "yellow", 'type' => 'info']);
            }

            $existingNumero = unidad::where('numeroUnidad', $request->numeroUnidad)
            ->first();

        if($existingNumero){
            // Unidad ya existe con un número igual pero diferentes ruta y directivo
            return redirect()->route('principal.unidades')->with(['message' => "Ya existe una unidad con el número proporcionado, pero con una ruta y directivo diferente: " .$request->numeroUnidad, "color" => "yellow", 'type' => 'info']);
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
            /* $formacionUnidad = new formacionunidades();
            $formacionUnidad->idUnidad = $unidad->idUnidad; // Utilizamos el idUnidad de la unidad recién creada
            $formacionUnidad->save(); */
            
            return redirect()->route('principal.unidades')->with(['message' => "Unidad agregada correctamente: " . $request->numeroUnidad, "color" => "green", 'type' => 'success']);
        } catch(Exception $e){
            return redirect()->route('principal.unidades')->with(['message' => "Error al agregar la unidad " .$request->numeroUnidad, "color" => "red", 'type' => 'error']);
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
            return redirect()->route('principal.operadores')->with(['message' => "El operador ya está registrado: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, "color" => "yellow", 'type' => 'info']);
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
            return redirect()->route('principal.operadores')->with(['message' => "Operador agregado correctamente: $nombreCompleto", "color" => "green", 'type' => 'success']);
        }catch(Exception $e){
            return redirect()->route('principal.operadores')->with(['message' => "Error al agregar al operador", "color" => "red", 'type' => 'error']);
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
                return redirect()->route('principal.sociosPrestadores')->with(['message' => "El directivo ya está registrado: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, "color" => "yellow", 'type' => 'info']);
            }
    
            $directivo = new directivo();
            $directivo->nombre = $request->nombre;
            $directivo->apellidoP = $request->apellidoP;
            $directivo->apellidoM = $request->apellidoM;
            $directivo->idTipoDirectivo = $request->tipDirectivo;
            $nombreCompleto =$directivo->apellidoP . ' ' . $directivo->apellidoM. ' ' . $directivo->nombre;
            $directivo->nombre_completo = $nombreCompleto;

            $directivo->save();
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "Directivo agregado correctamente: " .$request->nombre ." " .$request->apellidoP ." " .$request->apellidoM, "color" => "green", 'type' => 'success']);
        }catch(Exception $e){
            return redirect()->route('principal.sociosPrestadores')->with(['message' => "Error al agregar al directivo", "color" => "red", 'type' => 'error']);
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
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
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
                return redirect()->route('principal.rutas')->with(['message' => "La ruta ya está registrada: " . $request->nombreRuta, 'color' => 'yellow', 'type' => 'info']);
            }
    
            $ruta = new ruta();
            $ruta->nombreRuta = $request->nombreRuta;
            $ruta->save();
            return redirect()->route('principal.rutas')->with(['message' => "Ruta agregado correctamente: " .$request->nombreRuta, 'color' => 'green', 'type' => 'success']);
        }catch(Exception $e){
            return redirect()->back()->with(['message' => "Error al agregar la ruta: " . $e->getMessage(), 'color' => 'error', 'type' => 'error']);
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

    public function reportes()
    {
        $directivo = directivo::all();
        $operador = operador::all(); 
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        $unidad = unidad::all();
        $ruta = ruta::all();
        return Inertia::render('Principal/Reportes',[
            'unidad' => $unidad,
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'estado' => $estado,
            'ruta' => $ruta,
            'message' => session('message'),
            'color' => session('color'),
            'type' => session('type'),
        ]);
    }

}