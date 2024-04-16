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
        /*$unidad = unidad::all();
        $directivo = directivo::all();
        $operador = operador::all();
        $ruta = ruta::all();*/
        return Inertia::render('Principal/Inicio',[
            /*'unidad ' => $unidad,
            'directivo' => $directivo,
            'operador' => $operador,
            'ruta' => $ruta*/
        ]);
    }
    public function formarUnidades(){
        $unidad = unidad::all();
        $directivo = directivo::all();
        $operador = operador::all();
        $ruta = ruta::all();
        return Inertia::render('Principal/FormarUnidades',[
            'unidad ' => $unidad,
            'directivo' => $directivo,
            'operador' => $operador,
            'ruta' => $ruta
        ]);
    }
}
