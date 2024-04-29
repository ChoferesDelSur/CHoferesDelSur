<?php

use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;

//Route::get('/', [PrincipalController::class, 'inicio'])->name('inicio');
//Route::get('/', [PrincipalController::class, 'formarUnidades'])->name('formarUnidades');
Route::controller(PrincipalController::class)->group(function () {
    Route::get('/', 'inicio')->name('principal.inicio');
    Route::get('/principal/formUnidades', 'formarUnidades')->name('principal.formarUni');
    Route::get('/principal/unidades', 'unidades')->name('principal.unidades');
    Route::get('/principal/sociosPrestadores', 'sociosPrestadores')->name('principal.sociosPrestadores');
    Route::get('/principal/operadores', 'operadores')->name('principal.operadores');
    Route::get('/principal/rutas', 'rutas')->name('principal.rutas');

    Route::post('/principal/operadores', 'addOperador')->name('principal.addOperador');

    Route::post('/principal/unidades', 'addUnidad')->name('principal.addUnidad');
    
    Route::post('/principal/sociosPrestadores', 'addDirectivo')->name('principal.addDirectivo');

    Route::post('/principal/rutas', 'addRuta')->name('principal.addRuta');
    Route::put('/principal/rutas/actualizar/{idRuta}/edit', 'actualizarRuta')->name('principal.actualizarRuta');
    Route::delete('/principal/rutas/delete/{rutasIds}', 'eliminarRuta')->name('principal.eliminarRuta');
});