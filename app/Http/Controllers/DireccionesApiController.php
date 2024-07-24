<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\asentamiento;
use App\Models\codigoPostal;
use App\Models\entidad;
use App\Models\municipio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DireccionesApiController extends Controller
{

    //Obtencion de entidad, municipio, asentamiento y codigos postales
    public function consultarEntidades()
    {
        $entidad = entidad::all();
        return $entidad;
    }

    public function consultarasentamiento()
    {
        $asentamiento = asentamiento::all();
        return $asentamiento;
    }

    public function consultarmunicipio()
    {
        $municipio = municipio::all();
        return $municipio;
    }

    public function consultarCodigoPostal()
    {
        $codigosPostales = codigoPostal::all();
        return $codigosPostales;
    }
    //----------------------------------------------------------------
    // Obtencion de entidad, municipio y asentamiento por codigo postal
    public function obtenerentidadPorCodigoPostal($codigoPostal)
    {
        try {
            $entidad = asentamiento::whereHas('codigoPostal', function ($query) use ($codigoPostal) {
                $query->where('codigoPostal', $codigoPostal);
            })->firstOrFail()->municipio->entidad;

            return response()->json($entidad);
        } catch (ModelNotFoundException $e) {
            return response();
        }
    }

    public function obtenermunicipioPorCodigoPostal($codigoPostal)
    {
        $municipio = codigoPostal::where('codigoPostal', $codigoPostal)
            ->firstOrFail()
            ->asentamiento()
            ->with('municipio')
            ->get()
            ->pluck('municipio')
            ->unique();

        return response()->json($municipio);
    }

    public function obtenerasentamientoPorCodigoPostal($codigoPostal)
    {
        $asentamiento = codigoPostal::where('codigoPostal', $codigoPostal)
            ->firstOrFail()
            ->asentamiento;

        return response()->json($asentamiento);
    }
    //----------------------------------------------------------------
    // Obtener entidad, municipio y asentamiento por id de forma individual
    public function obtenerMunicipiosPorEstado($idEntidad)
    {
        return Cache::remember('municipios_entidad_' . $idEntidad, now()->addHours(24), function () use ($idEntidad) {
            $entidad = entidad::with('municipios')->find($idEntidad);
    
            if (!$entidad) {
                return response()->json(['message' => 'entidad no encontrado'], 404);
            }
    
            $municipios = $entidad->municipios->sortBy('municipio')->values()->all();
    
            return response()->json($municipios);
        });
    }

    public function obtenerAsentamientosPorMunicipio($idMunicipio)
    {
        return Cache::remember('asentamientos_municipio_' . $idMunicipio, now()->addHours(24), function () use ($idMunicipio) {
            $municipio = municipio::with('asentamientos')->find($idMunicipio);
    
            if (!$municipio) {
                return response()->json(['message' => 'Municipio no encontrado'], 404);
            }
    
            $asentamientos = $municipio->asentamientos->sortBy('asentamiento')->values()->all();
    
            return response()->json($asentamientos);
        });
    }

    // ----------------------------------------------------------------
    //Obtener todos los elementos anteriores por codPostal

    public function consDatosPorCodigoPostal($codigoPostal)
    {
        $datos = [];
        // Buscar el asentamiento por código postal
        $codigoPostalModel = codigoPostal::where('codigoPostal', $codigoPostal)->first();        
        if ($codigoPostalModel) {
            // Obtener el primer asentamiento asociado al código postal
            $asentamiento = $codigoPostalModel->asentamientos->first();            
            if ($asentamiento) {
                // Acceder al municipio a través de la relación en el modelo asentamiento
                $municipio = $asentamiento->municipios;
                $datos['municipio'] = $municipio;
                if ($municipio) {
                    // Acceder al entidad a través de la relación en el modelo municipio
                    $entidad = $municipio->estados;                  
                    if ($entidad) {
                        $datos['entidad'] = $entidad;
                    }
                }
            }
        }
        return response()->json($datos);
    }

    // ----------------------------------------------------------------
    public function informacionAsentamiento($idAsentamiento){
        $asentamiento = asentamiento::find($idAsentamiento);
        $asentamiento->codPos = $asentamiento->codigoPostal->codigoPostal;
        return response()->json($asentamiento);
    }
}
