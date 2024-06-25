<?php

use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\ServicioController;
use App\Http\Middleware\AdminMiddleware;


Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'index')->name('inicioSesion');
    Route::post('/', 'login');
    Route::get('/logout', 'logout')->name('cerrarSesion');
    Route::post('/register', 'register')->name('registrarse');
});

Route::middleware([AdminMiddleware::class])->group(function () {
    Route::controller(PrincipalController::class)->group(function () {
        Route::get('/principal', 'inicio')->name('principal.inicio');//Se agrego principal
        Route::get('/principal/unidades', 'unidades')->name('principal.unidades');
        Route::get('/principal/sociosPrestadores', 'sociosPrestadores')->name('principal.sociosPrestadores');
        Route::get('/principal/operadores', 'operadores')->name('principal.operadores');
    
        Route::post('/principal/operadores', 'addOperador')->name('principal.addOperador');
        Route::put('/principal/operadores/{idOperador}/edit', 'actualizarOperador')->name('principal.actualizarOperador');
        Route::delete('/principal/operadores/{operadoresIds}', 'eliminarOperador')->name('principal.eliminarOperador');
    
        Route::post('/principal/unidades', 'addUnidad')->name('principal.addUnidad');
        Route::put('/principal/unidades/{idUnidad}/edit', 'actualizarUnidad')->name('principal.actualizarUnidad');
        Route::delete('/principal/unidades/{unidadesIds}', 'eliminarUnidad')->name('principal.eliminarUnidad');
        Route::post('/principal/unidades/asignarOper', 'asignarOperador')->name('principal.asignarOperador');
        Route::post('/principal/unidades/quitarOper', 'quitarOperador')->name('principal.quitarOperador');
        
        Route::post('/principal/sociosPrestadores', 'addDirectivo')->name('principal.addDirectivo');
        Route::put('/principal/sociosPrestadores/{idDirectivo}/edit', 'actualizarDirectivo')->name('principal.actualizarDirectivo');
        Route::delete('/principal/sociosPrestadores/{directivosIds}', 'eliminarDirectivo')->name('principal.eliminarDirectivo');
    
        Route::get('/principal/rutas', 'rutas')->name('principal.rutas');
        Route::post('/principal/rutas', 'addRuta')->name('principal.addRuta');
        Route::put('/principal/rutas/{idRuta}/edit','actualizarRuta')->name('principal.actualizarRuta');
        Route::delete('/principal/rutas/{rutasIds}', 'eliminarRuta')->name('principal.eliminarRuta');
    
        Route::get('/principal/formUnidades', 'formarUnidades')->name('principal.formarUni');
        Route::post('/principal/formarUnidades', 'registrarHoraEntrada')->name('principal.registarHoraEntrada');
        Route::post('/principal/formarUnidades/corte', 'registrarCorte')->name('principal.registarCorte');
        Route::post('/principal/formarUnidades/regreso', 'RegistrarHoraRegreso')->name('principal.registrarHoraRegreso');
        Route::post('/principal/formarUnidades/regUC', 'registrarUC')->name('principal.registrarUC');
        Route::post('/principal/formarUnidades/regresoUC', 'RegistrarHoraRegresoUC')->name('principal.regresoUC');
        Route::post('/principal/formarUnidades/regCastigo', 'registrarCastigo')->name('principal.registrarCastigo');
        Route::post('/principal/formarUnidades/trabajaDomingo', 'registrarTrabajanDomingo')->name('principal.trabDomingo');
    
        Route::get('/principal/reportes', 'reportes')->name('principal.reportes');
        Route::get('/principal/administrarUsuarios', 'adminUsuarios')->name('principal.administrarUsuarios');
    });
});

Route::controller(ReporteController::class)->group(function(){
    Route::get('/reporte/entradas-semana/unidad/{idUnidad}/semana/{semana}','obtenerEntradasUnidadPorSemana')->name('reportes.entradasSemana');
    Route::get('/reporte/entradas-mes/unidad/{idUnidad}/mes/{mes}','obtenerEntradasUnidadPorMes')->name('reportes.entradasMes');
    Route::get('/reporte/entradas/unidad/{idUnidad}/anio/{anio}','obtenerEntradasUnidadPorAnio')->name('reportes.entradasAnio');

    Route::get('/reporte/entradasT-semana/unidad/{idUnidad}/semana/{semana}','obtenerEntradasTardesPorSemana')->name('reportes.entradasTardesSemana');
    Route::get('/reporte/entradasT-mes/unidad/{idUnidad}/mes/{mes}','obtenerEntradasTardesPorMes')->name('reportes.entradasTardesMes');
    Route::get('/reporte/entradasT/unidad/{idUnidad}/anio/{anio}','obtenerEntradasTardesPorAnio')->name('reportes.entradasTardesAnio');

    Route::get('/reporte/cortes-semana/unidad/{idUnidad}/semana/{semana}','obtenerCortesPorSemana')->name('reportes.cortesSemana');
    Route::get('/reporte/cortes-mes/unidad/{idUnidad}/mes/{mes}','obtenerCortesPorMes')->name('reportes.cortesMes');
    Route::get('/reporte/cortes/unidad/{idUnidad}/anio/{anio}','obtenerCortesPorAnio')->name('reportes.cortesAnio');

    Route::get('/reporte/cortesCR-semana/unidad/{idUnidad}/semana/{semana}','obtenerCortesCRPorSemana')->name('reportes.cortesCRSemana');
    Route::get('/reporte/cortesCR-mes/unidad/{idUnidad}/mes/{mes}','obtenerCortesCRPorMes')->name('reportes.cortesCRMes');
    Route::get('/reporte/cortesCR/unidad/{idUnidad}/anio/{anio}','obtenerCortesCRPorAnio')->name('reportes.cortesCRAnio');

    Route::get('/reporte/cortesSR-semana/unidad/{idUnidad}/semana/{semana}','obtenerCortesSRPorSemana')->name('reportes.cortesSRSemana');
    Route::get('/reporte/cortesSR-mes/unidad/{idUnidad}/mes/{mes}','obtenerCortesSRPorMes')->name('reportes.cortesSRMes');
    Route::get('/reporte/cortesSR/unidad/{idUnidad}/anio/{anio}','obtenerCortesSRPorAnio')->name('reportes.cortesSRAnio');

    Route::get('/reporte/DiasTrabajados-semana/operador/{idOperador}/semana/{semana}','obtenerDiasTrabajadosPorSemana')->name('reportes.diasTrabajadosSemana');
    Route::get('/reporte/DiasTrabajados-mes/operador/{idOperador}/mes/{mes}','obtenerDiasTrabajadosPorMes')->name('reportes.diasTrabajadosMes');
    Route::get('/reporte/DiasTrabajados/operador/{idOperador}/anio/{anio}','obtenerDiasTrabajadosPorAnio')->name('reportes.diasTrabajadosAnio');

    Route::get('/reporte/castigos-semana/unidad/{idUnidad}/semana/{semana}','obtenerCastigosPorSemana')->name('reportes.castigosSemana');
    Route::get('/reporte/castigos-mes/unidad/{idUnidad}/mes/{mes}','obtenerCastigosPorMes')->name('reportes.castigosMes');
    Route::get('/reporte/castigos/unidad/{idUnidad}/anio/{anio}','obtenerCastigosPorAnio')->name('reportes.castigosAnio');

    Route::get('/reporte/UC-semana/unidad/{idUnidad}/semana/{semana}','obtenerUCPorSemana')->name('reportes.UCSemana');
    Route::get('/reporte/UC-mes/unidad/{idUnidad}/mes/{mes}','obtenerUCPorMes')->name('reportes.UCMes');
    Route::get('/reporte/UC/unidad/{idUnidad}/anio/{anio}','obtenerUCPorAnio')->name('reportes.UCAnio');
});

Route::controller(InfoController::class)->group(
    function () {
        Route::get('obtener/info/usuario/login', 'obtenerUsuario')->name('obtenerUsuario');
        Route::get('obtener/info/tipoUsuario/{idTipoUsuario}', 'obtenerTipoUsuario')->name('obtenerTipoUsuario');
    }
);

Route::controller(ServicioController::class)->group(function(){
    Route::get('/servicio', 'inicio')->name('servicio.inicio');
});