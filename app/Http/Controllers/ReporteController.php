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

    public function obtenerEntradasUnidadPorMes($idUnidad)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $entradas = Entrada::with(['unidad.operador', 'unidad.ruta', 'unidad.directivo'])
            ->where('idUnidad', $idUnidad)
            ->whereBetween('horaEntrada', [$startDate, $endDate])
            ->get(); // Incluir created_at

        return response()->json($entradas);
    }
}
