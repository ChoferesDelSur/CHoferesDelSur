<?php

namespace App\Http\Controllers;

use App\Models\unidad;
use App\Models\directivo;
use App\Models\operador;
use App\Models\calificacion;
use App\Models\estado;
use App\Models\usuario;
use App\Models\formacionunidades;
use App\Models\movimiento;
use App\Models\ruta;
use App\Models\tipoDirectivo;
use App\Models\tipoOperador;
use App\Models\castigo;
use App\Models\corte;
use App\Models\entrada;
use App\Models\rolServicio;
use App\Models\ultimaCorrida;
use App\Models\tipoUltimaCorrida;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function obtenerEntradasUnidadPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $entradas = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioSemana, $finSemana])
                ->get();

            return response()->json($entradas);
        } catch (\Exception $e) {
            Log::error('Error al obtener entradas por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener entradas por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerEntradasUnidadPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener las entradas filtradas por idUnidad y por el rango de fechas en created_at
            $entradas = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->get();
    
            // Devolver las entradas filtradas como respuesta JSON
            return response()->json($entradas);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener entradas por mes'], 500);
        }
    }

    public function obtenerEntradasUnidadPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener las entradas filtradas por idUnidad y por el rango de fechas en created_at
            $entradas = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->get();

            // Devolver las entradas filtradas como respuesta JSON
            return response()->json($entradas);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener entradas por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerEntradasTardesPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $entradasTardes = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->where('horaEntrada', '>=', '07:01:00') // Hora mayor o igual a 7:01 AM
                ->where('created_at', '>', $inicioSemana)
                ->where('created_at', '<', $finSemana)
                ->get();

            return response()->json($entradasTardes);
        } catch (\Exception $e) {
            Log::error('Error al obtener entradas tardías por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener entradas tardías por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerEntradasTardesPorMes($idUnidad, $mes)
    {
        try {
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();

            $entradasTardes = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->where('horaEntrada', '>=', '07:01:00') // Hora mayor o igual a 7:01 AM
                ->where('created_at', '>=', $inicioMes)
                ->where('created_at', '<=', $finMes)
                ->get();

            return response()->json($entradasTardes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener entradas tardías por mes'], 500);
        }
    }

    public function obtenerEntradasTardesPorAnio($idUnidad, $anio)
    {
        try {
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            $entradasTardes = entrada::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->where('horaEntrada', '>=', '07:01:00') // Hora mayor o igual a 7:01 AM
                ->where('created_at', '>', $inicioAnio)
                ->where('created_at', '<', $finAnio)
                ->get();

            return response()->json($entradasTardes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener entradas tardías por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $cortes = corte::with(['ruta','directivo','operador','unidad.entradas','unidad'])
            ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                return $query->where('idUnidad', $idUnidad);
            })
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->get();

            return response()->json($cortes);
        } catch (\Exception $e) {
            Log::error('Error al obtener cortes por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener cortes por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->get();
    
            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por mes'], 500);
        }
    }

    public function obtenerCortesPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->get();

            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesCRPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $cortes = corte::with(['ruta','directivo','operador','unidad.entradas', 'unidad'])
            ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                return $query->where('idUnidad', $idUnidad);
            })
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->whereNotNull('horaRegreso') 
            ->get();

            return response()->json($cortes);
        } catch (\Exception $e) {
            Log::error('Error al obtener cortes por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener cortes por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesCRPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->whereNotNull('horaRegreso') 
                ->get();
    
            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por mes'], 500);
        }
    }

    public function obtenerCortesCRPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->whereNotNull('horaRegreso') 
                ->get();

            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesSRPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $cortes = corte::with(['ruta','directivo','operador','entradas','unidad'])
            ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                return $query->where('idUnidad', $idUnidad);
            })
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->whereNull('horaRegreso') 
            ->get();

            return response()->json($cortes);
        } catch (\Exception $e) {
            Log::error('Error al obtener cortes por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener cortes por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCortesSRPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->whereNull('horaRegreso') 
                ->get();
    
            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por mes'], 500);
        }
    }

    public function obtenerCortesSRPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener los cortes filtradas por idUnidad y por el rango de fechas en created_at
            $cortes = corte::with(['ruta', 'directivo', 'unidad.entradas', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->whereNull('horaRegreso') 
                ->get();

            // Devolver los cortes filtradas como respuesta JSON
            return response()->json($cortes);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener cortes por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerDiasTrabajadosPorSemana($idOperador, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            // Obtener todos los operadores
            $operadores = operador::all();

            $diasTrabajadosQuery = entrada::whereBetween('created_at', [$inicioSemana, $finSemana])
                ->select(DB::raw('DATE(created_at) as date'), 'idOperador')
                ->distinct();

            if ($idOperador !== 'todas') {
                $diasTrabajadosQuery->where('idOperador', $idOperador);
            }

            $diasTrabajados = $diasTrabajadosQuery->get()
                ->groupBy('idOperador')
                ->map(function ($dias, $idOperador) {
                    return [
                        'nombre_completo' => operador::find($idOperador)->nombre_completo,
                        'diasTrabajados' => $dias->count()
                    ];
                })->values();

            return response()->json($diasTrabajados);
        } catch (\Exception $e) {
            Log::error('Error al obtener días trabajados por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener días trabajados por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerDiasTrabajadosPorMes($idOperador, $mes)
    {
        try {
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();

            $diasTrabajadosQuery = entrada::whereBetween('created_at', [$inicioMes, $finMes])
                ->select(DB::raw('DATE(created_at) as date'), 'idOperador')
                ->distinct();

            if ($idOperador !== 'todas') {
                $diasTrabajadosQuery->where('idOperador', $idOperador);
            }

            $diasTrabajados = $diasTrabajadosQuery->get()
                ->groupBy('idOperador')
                ->map(function ($dias, $idOperador) {
                    return [
                        'nombre_completo' => operador::find($idOperador)->nombre_completo,
                        'diasTrabajados' => $dias->count()
                    ];
                })->values();

            return response()->json($diasTrabajados);
        } catch (\Exception $e) {
            Log::error('Error al obtener días trabajados por mes', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener días trabajados por mes', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerDiasTrabajadosPorAnio($idOperador, $anio)
    {
        try {
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            $diasTrabajadosQuery = entrada::whereBetween('created_at', [$inicioAnio, $finAnio])
                ->select(DB::raw('DATE(created_at) as date'), 'idOperador')
                ->distinct();

            if ($idOperador !== 'todas') {
                $diasTrabajadosQuery->where('idOperador', $idOperador);
            }

            $diasTrabajados = $diasTrabajadosQuery->get()
                ->groupBy('idOperador')
                ->map(function ($dias, $idOperador) {
                    return [
                        'nombre_completo' => operador::find($idOperador)->nombre_completo,
                        'diasTrabajados' => $dias->count()
                    ];
                })->values();

            return response()->json($diasTrabajados);
        } catch (\Exception $e) {
            Log::error('Error al obtener días trabajados por año', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener días trabajados por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCastigosPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $castigos = castigo::with(['ruta', 'directivo', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioSemana, $finSemana])
                ->get();

            return response()->json($castigos);
        } catch (\Exception $e) {
            Log::error('Error al obtener los castigos por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener castigos por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerCastigosPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener los castigos filtradas por idUnidad y por el rango de fechas en created_at
            $castigos = castigo::with(['ruta', 'directivo', 'operador', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->get();
    
            // Devolver las entradas filtradas como respuesta JSON
            return response()->json($castigos);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener castigos por mes'], 500);
        }
    }

    public function obtenerCastigosPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener los castigos filtradas por idUnidad y por el rango de fechas en created_at
            $castigos = castigo::with(['ruta', 'directivo', 'operador','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->get();

            // Devolver los castigos filtradas como respuesta JSON
            return response()->json($castigos);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener castigos por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerUCPorSemana($idUnidad, $semana)
    {
        try {
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $ultimasCorridas = ultimaCorrida::with(['ruta', 'directivo', 'operador', 'tipoUltimaCorrida','unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioSemana, $finSemana])
                ->get();

            return response()->json($ultimasCorridas);
        } catch (\Exception $e) {
            Log::error('Error al obtener las ultimas corridas por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener las ultimas corridas por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerUCPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes)->startOfMonth();
            $finMes = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener las ultimas corridas filtradas por idUnidad y por el rango de fechas en created_at
            $ultimasCorridas = ultimaCorrida::with(['ruta', 'directivo', 'operador', 'tipoUltimaCorrida', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->get();
    
            // Devolver las ultimas corridas filtradas como respuesta JSON
            return response()->json($ultimasCorridas);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener las ultimas corridas por mes'], 500);
        }
    }

    public function obtenerUCPorAnio($idUnidad, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();

            // Obtener las ultimas corridas filtradas por idUnidad y por el rango de fechas en created_at
            $ultimasCorridas = ultimaCorrida::with(['ruta', 'directivo', 'operador', 'tipoUltimaCorrida', 'unidad'])
                ->when($idUnidad !== 'todas', function ($query) use ($idUnidad) {
                    return $query->where('idUnidad', $idUnidad);
                })
                ->whereBetween('created_at', [$inicioAnio, $finAnio])
                ->get();

            // Devolver las ultimas corridas filtradas como respuesta JSON
            return response()->json($ultimasCorridas);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener las ultimas corridas por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerMovimientosPorAnio($idDirectivo, $anio)
    {
        try {
            // Crear las fechas de inicio y fin del año seleccionado
            $inicioAnio = Carbon::create($anio, 1, 1)->startOfYear();
            $finAnio = Carbon::create($anio, 12, 31)->endOfYear();
            // Obtener los movimientos filtrados por idDirectivo y por el rango de fechas en fechaMovimiento
            $movimientos = movimiento::with(['operador', 'directivo', 'tipoMovimiento', 'estado'])
                ->when($idDirectivo !== 'todas', function ($query) use ($idDirectivo) {
                    return $query->where('idDirectivo', $idDirectivo);
                })
                ->whereBetween('fechaMovimiento', [$inicioAnio, $finAnio])//tenia fechaMovimiento
                ->get();
            // Devolver los movimientos filtrados como respuesta JSON
            return response()->json($movimientos);//Estaba $reportes
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener los movimientos por año', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerMovimientosPorMes($idDirectivo, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $inicioMes = Carbon::create(null, $mes, 1)->startOfMonth();
            $finMes = Carbon::create(null, $mes, 1)->endOfMonth();
            // Obtener los movimientos filtrados por idDirectivo y por el rango de fechas en fechaMovimiento
            $movimientos = movimiento::with(['operador', 'directivo', 'tipoMovimiento', 'estado'])
                ->when($idDirectivo !== 'todas', function ($query) use ($idDirectivo) {
                    return $query->where('idDirectivo', $idDirectivo);
                })
                ->whereBetween('fechaMovimiento', [$inicioMes, $finMes])
                ->get();

            return response()->json($movimientos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los movimientos por mes', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerMovimientosPorSemana($idDirectivo, $semana)
    {
        try {
            // Calcular el inicio y fin de la semana
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            // Obtener los movimientos filtrados por idDirectivo y por el rango de fechas en fechaMovimiento
            $movimientos = movimiento::with(['operador', 'directivo', 'tipoMovimiento', 'estado'])
                ->when($idDirectivo !== 'todas', function ($query) use ($idDirectivo) {
                    return $query->where('idDirectivo', $idDirectivo);
                })
                ->whereBetween('fechaMovimiento', [$inicioSemana, $finSemana])
                ->get();

            return response()->json($movimientos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los movimientos por semana', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerMovimientosPorDia($idDirectivo, $dia)
    {
        try {
            // Convertir la fecha proporcionada de DD-MM-YYYY a un formato de Carbon
            $diaCarbon = Carbon::createFromFormat('d-m-Y', $dia);
    
            // Validar que la fecha se ha convertido correctamente
            if (!$diaCarbon) {
                return response()->json(['error' => 'Formato de fecha inválido'], 400);
            }
    
            // Consulta para obtener los movimientos filtrados
            $movimientos = movimiento::with(['operador', 'directivo', 'tipoMovimiento', 'estado'])
                ->when($idDirectivo !== 'todas', function ($query) use ($idDirectivo) {
                    return $query->where('idDirectivo', $idDirectivo);
                })
                ->whereDate('fechaMovimiento', $diaCarbon->format('Y-m-d'))
                ->get();
    
            return response()->json($movimientos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los movimientos por fecha', 'details' => $e->getMessage()], 500);
        }
    }

    public function obtenerOperadoresSinTrabajarPorSemana($semana)
    {
        try {
            // Calcular el inicio y fin de la semana (lunes a viernes)
            $inicioSemana = Carbon::now()->startOfYear()->addWeeks($semana - 1)->startOfWeek();
            $finSemana = $inicioSemana->copy()->addDays(4); // Solo hasta el viernes
    
            // Obtener el id del estado "Alta"
            $estadoAlta = estado::where('estado', 'Alta')->first();
    
            // Obtener el id del tipo de operador "Base"
            $tipoOperadorBase = tipoOperador::where('tipOperador', 'Base')->first();
    
            // Obtener operadores en estado Alta y tipo Base que trabajaron al menos un día de lunes a viernes
            $operadoresConEntradas = entrada::whereBetween('created_at', [$inicioSemana, $finSemana])
                ->whereHas('operador', function ($query) use ($estadoAlta, $tipoOperadorBase) {
                    $query->where('idEstado', $estadoAlta->idEstado)
                        ->where('idTipoOperador', $tipoOperadorBase->idTipoOperador);
                })
                ->pluck('idOperador')
                ->unique();
    
            // Obtener todos los operadores en estado Alta y tipo Base
            $todosOperadoresAlta = operador::where('idEstado', $estadoAlta->idEstado)
                ->where('idTipoOperador', $tipoOperadorBase->idTipoOperador)
                ->get();
    
            // Filtrar los operadores que no trabajaron de lunes a viernes
            $operadoresSinTrabajar = $todosOperadoresAlta->filter(function ($operador) use ($operadoresConEntradas) {
                return !$operadoresConEntradas->contains($operador->idOperador);
            })->map(function ($operador) {
                // Obtener la última entrada de cada operador
                $ultimaEntrada = entrada::where('idOperador', $operador->idOperador)
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                // Si existe una entrada, obtener la fecha del último día trabajado
                $ultimoDiaTrabajado = $ultimaEntrada ? $ultimaEntrada->created_at->format('Y-m-d') : 'No ha trabajado';
    
                return [
                    'idOperador' => $operador->idOperador,
                    'nombre_completo' => $operador->nombre_completo,
                    'ultimo_dia_trabajado' => $ultimoDiaTrabajado, // Se agrega la fecha
                ];
            });
    
            // Verificar si el operador tiene un registro de entrada hoy, y agregarlo si no se le considera
            $fechaHoy = Carbon::now()->startOfDay();  // El inicio del día de hoy
            $entradaHoy = entrada::where('created_at', '>=', $fechaHoy)
                ->whereHas('operador', function ($query) use ($estadoAlta, $tipoOperadorBase) {
                    $query->where('idEstado', $estadoAlta->idEstado)
                        ->where('idTipoOperador', $tipoOperadorBase->idTipoOperador);
                })
                ->pluck('idOperador')
                ->unique();
    
            // Asegurarse de que los operadores que registraron entrada hoy no se excluyan
            $operadoresConEntradas = $operadoresConEntradas->merge($entradaHoy);
    
            // Filtrar los operadores que no trabajaron de lunes a viernes
            $operadoresSinTrabajar = $todosOperadoresAlta->filter(function ($operador) use ($operadoresConEntradas) {
                return !$operadoresConEntradas->contains($operador->idOperador);
            })->map(function ($operador) {
                // Obtener la última entrada de cada operador
                $ultimaEntrada = entrada::where('idOperador', $operador->idOperador)
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                // Si existe una entrada, obtener la fecha del último día trabajado
                $ultimoDiaTrabajado = $ultimaEntrada ? $ultimaEntrada->created_at->format('Y-m-d') : 'No ha trabajado';
    
                return [
                    'idOperador' => $operador->idOperador,
                    'nombre_completo' => $operador->nombre_completo,
                    'ultimo_dia_trabajado' => $ultimoDiaTrabajado, // Se agrega la fecha
                ];
            });
    
            return response()->json($operadoresSinTrabajar);
        } catch (\Exception $e) {
            Log::error('Error al obtener operadores sin trabajar por semana', ['details' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener operadores sin trabajar por semana', 'details' => $e->getMessage()], 500);
        }
    }    

    public function reporteMultasDominicales($semana)
    {
        try {
            // Calcular el domingo y el sábado de la semana especificada
            $domingoEspecifico = Carbon::now()->startOfYear()->addWeeks($semana - 1)->previous(Carbon::SUNDAY);
            $sabadoEspecifico = $domingoEspecifico->copy()->subDay();
            $lunesDespuesDelDomingo = $domingoEspecifico->copy()->addDay();
    
            // Obtener unidades que deberían trabajar el domingo pero no lo hicieron
            $unidadesSancionables = unidad::with(['operador', 'rolServicio', 'ruta', 'directivo'])
                ->whereHas('rolServicio', function($query) {
                    $query->where('trabajaDomingo', 'SI'); // Unidades que trabajan los domingos
                })
                ->whereDoesntHave('entradas', function($query) use ($domingoEspecifico) {
                    $query->whereDate('created_at', '=', $domingoEspecifico); // No registraron entrada el domingo
                })
                ->get();
    
            // Filtrar unidades sancionables basándose en las entradas del sábado y del lunes
            $unidadesFiltradas = $unidadesSancionables->filter(function($unidad) use ($sabadoEspecifico, $lunesDespuesDelDomingo) {
                // Verificar si la unidad registró entrada el sábado y obtener el registro
                $entradaSabado = $unidad->entradas()
                    ->with('operador')
                    ->with('ruta')
                    ->whereDate('created_at', '=', $sabadoEspecifico)->first();
    
                // Verificar si la unidad registró entrada el lunes antes de las 10 a.m. y obtener el registro
                $entradaLunesTemprana = $unidad->entradas()
                    ->with('operador')
                    ->with('ruta')
                    ->whereDate('created_at', '=', $lunesDespuesDelDomingo)
                    ->whereTime('horaEntrada', '<', '10:00:00')
                    ->first();
    
                // Si existen las entradas, las almacenamos en la unidad
                if ($entradaSabado) {
                    $unidad->entradaSabado = $entradaSabado;
                }
    
                if ($entradaLunesTemprana) {
                    $unidad->entradaLunesTemprana = $entradaLunesTemprana;
                }
    
                // Mantener en el listado solo si cumple con ambas condiciones
                return $entradaSabado && $entradaLunesTemprana;
            });
    
            // Devolver el resultado
            return response()->json($unidadesFiltradas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    
}
