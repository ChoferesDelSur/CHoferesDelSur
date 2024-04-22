<?php

namespace App\Http\Controllers;

use App\Models\unidad;
use App\Models\directivo;
use App\Models\operador;
use App\Models\calificacion;
use App\Models\corte;
use App\Models\entrada;
use App\Models\estado;
use App\Models\formacionunidades;
use App\Models\ruta;
use App\Models\tipodirectivo;
use App\Models\tipooperador;
use Illuminate\Http\Request;
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
        /* $unidad = unidad::all();
        $directivo = directivo::all();
        $operador = operador::all();
        $ruta = ruta::all(); */
        return Inertia::render('Principal/FormarUnidades',[
            /* 'unidad ' => $unidad,
            'directivo' => $directivo,
            'operador' => $operador,
            'ruta' => $ruta */
        ]);
    }
    public function unidades(){
        $unidad = unidad::all();
        $operador = operador::all(); 
        // Filtrar los operadores disponibles
        $operadoresDisp = operador::where('idEstado', 1) // Filtrar por estado "Alta"
                                ->whereDoesntHave('unidad') // Verificar que no estén relacionados con otra unidad
                                ->get();
        $ruta = ruta::all();
        return Inertia::render('Principal/Unidades',[
            'unidad' => $unidad,
            'operador' => $operador,
            'operadoresDisp' => $operadoresDisp,
            'ruta' => $ruta
        ]);
    }

    public function addUnidad(Request $request){
        try{
            $request->validate([
                'numeroUnidad'=> 'required',
                'ruta' => 'required',
                'operador' => 'required',
            ]);

            $numeroUnidad = $request->numeroUnidad;
    
            $unidad = new unidad();
            $unidad->numeroUnidad = $numeroUnidad;
            $unidad->idOperador = $request->operador;
            $unidad->idRuta = $request->ruta;
            $unidad->save();
            return redirect()->route('principal.unidades')->with(['message' => "Unidad agregado correctamente: $numeroUnidad ", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.unidades');
        }
    }

    public function operadores(){
        $operador = operador::all(); 
        $tipoOperador = tipooperador::all();
        $estado = estado::all();
        return Inertia::render('Principal/Operadores',[
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'estado' => $estado,
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
            ]);

            $nombre = $request->nombre;
            $apellidoP = $request->apellidoP;
            $apellidoM = $request->apellidoM;
    
            $operador = new operador();
            $operador->nombre = $nombre;
            $operador->apellidoP = $apellidoP;
            $operador->apellidoM = $apellidoM;
            $operador->idTipoOperador = $request->tipoOperador;
            $operador->idEstado = $request->estado;
            $operador->save();
            return redirect()->route('principal.operadores')->with(['message' => "Operador agregado correctamente: $nombre $apellidoP $apellidoM", "color" => "green"]);
        }catch(Exception $e){
            return redirect()->route('principal.operadores');
        }
    }

    public function rutas(){
        $ruta = ruta::all();
        return Inertia::render('Principal/Rutas',[
            'ruta' => $ruta,
        ]);
    }


}