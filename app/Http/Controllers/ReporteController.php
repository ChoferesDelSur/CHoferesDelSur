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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function obtenerEntradasUnidad($idUnidad)
    {
        /* dd("idUnidad en obtenerEntradaUnidad",$idUnidad); */
        $entradas = Entrada::with(['unidad.operador', 'unidad.ruta', 'unidad.directivo'])
        ->where('idUnidad', $idUnidad)
        ->get(); // Incluir created_at

    return response()->json($entradas);
    }

    public function obtenerEntradasUnidadPorSemana($idUnidad)
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $entradas = Entrada::with(['unidad.operador', 'unidad.ruta', 'unidad.directivo'])
            ->where('idUnidad', $idUnidad)
            ->whereBetween('horaEntrada', [$startDate, $endDate])
            ->get(); // Incluir created_at

        return response()->json($entradas);
    }

    public function obtenerEntradasUnidadPorMes($idUnidad, $mes)
    {
        try {
            // Crear las fechas de inicio y fin del mes seleccionado
            $startDate = Carbon::create(null, $mes)->startOfMonth();
            $endDate = Carbon::create(null, $mes)->endOfMonth();
    
            // Obtener las entradas filtradas por idUnidad y por el rango de fechas en created_at
            $entradas = Entrada::with(['unidad.operador', 'unidad.ruta', 'unidad.directivo'])
                ->where('idUnidad', $idUnidad)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
    
            // Devolver las entradas filtradas como respuesta JSON
            return response()->json($entradas);
        } catch (\Exception $e) {
            // Manejar excepciones y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener entradas por mes'], 500);
        }
    }
}
