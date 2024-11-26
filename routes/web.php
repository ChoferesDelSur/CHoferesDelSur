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
use App\Http\Controllers\RHController;
use App\Http\Controllers\DirectivoController;
use App\Http\Controllers\DireccionesApiController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ServicioMiddleware;
use App\Http\Middleware\RHMiddleware;
use App\Http\Middleware\DirectivoMiddleware;
use App\Http\Middleware\RedurectToHttps;

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
        Route::post('/principal/unidades/asignarOper', 'asignarOperador')->name('principal.asignarOperador');
        Route::post('/principal/unidades/quitarOper', 'quitarOperador')->name('principal.quitarOperador');
    
        Route::post('/principal/unidades', 'addUnidad')->name('principal.addUnidad');
        Route::put('/principal/unidades/{idUnidad}/edit', 'actualizarUnidad')->name('principal.actualizarUnidad');
        Route::delete('/principal/unidades/{unidadesIds}', 'eliminarUnidad')->name('principal.eliminarUnidad');
        
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
        Route::post('/principal/usuarios','agregarUsuario')->name('principal.addUsuario');
        Route::delete('/principal/usuarios/{idUsuario}', 'eliminarUsuario')->name('principal.eliminarUsuario');
        Route::put('/principal/usuarios/restaurar-usuario/{idUsuario}', 'restaurarUsuario')->name('principal.restUsuario');
        Route::put('/principal/usuarios/{idUsuario}/edit', 'actualizarUsuario')->name('principal.actualizarUsuario');
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

    Route::get('/reporte/Movimientos/directivo/{idDirectivo}/anio/{anio}','obtenerMovimientosPorAnio')->name('reportes.MovAnio');
    Route::get('/reporte/Movimientos/directivo/{idDirectivo}/semana/{semana}','obtenerMovimientosPorSemana')->name('reportes.MovSemana');
    Route::get('/reporte/Movimientos/directivo/{idDirectivo}/mes/{mes}','obtenerMovimientosPorMes')->name('reportes.MovMes');
    Route::get('/reporte/Movimientos/directivo/{idDirectivo}/dia/{dia}','obtenerMovimientosPorDia')->name('reportes.MovDia');

    Route::get('/reporte/OperadoresSinTrabajar/operadores/semana/{semana}','obtenerOperadoresSinTrabajarPorSemana')->name('reporte.opSinTrabajar');
    Route::get('/reporte/multasDominicales/semana/{semana}','reporteMultasDominicales')->name('reporte.multasDominicales');

    Route::get('/reporte/concentrado-semana/unidad/{idUnidad}/semana/{semana}','obtenerConcentradoPorSemana')->name('reportes.concentradoSemana');
    Route::get('/reporte/concentrado-mes/unidad/{idUnidad}/mes/{mes}','obtenerConcentradoPorMes')->name('reportes.concentradoMes');
    Route::get('/reporte/concentrado/unidad/{idUnidad}/anio/{anio}','obtenerConcentradoPorAnio')->name('reportes.concentradoAnio');

});

Route::controller(InfoController::class)->group(function () {
        Route::get('obtener/info/usuario/login', 'obtenerUsuario')->name('obtenerUser');
        Route::get('obtener/info/tipoUsuario/{idTipoUsuario}', 'obtenerTipoUsuario')->name('obtenerTipoUser');
    }
);

//Rutas para obtener los estados, municipios, asentamientos y codigos postales
Route::controller(DireccionesApiController::class)->group(function () {
    // Ruta para obtener todos los estados
    Route::get('obtener/estados', 'consultarEntidades')->name('consEstados');

    // Rutas para encontrar Estados, municipios, asentamientos por codigo postal
    Route::get('obtener/estado/codigoPostal/{codigoPostal}', 'obtenerEstadoPorCodigoPostal')->name('consEstadoXCodPostal');
    Route::get('obtener/municipios/codigoPostal/{codigoPostal}', 'obtenerMunicipiosPorCodigoPostal')->name('consMunicipiosXCodPostal');
    Route::get('obtener/asentamientos/codigoPostal/{codigoPostal}', 'obtenerAsentamientosPorCodigoPostal')->name('consAsentamientosXCodPostal');

    //Rutas para encotrar municipios y asentamientos por el codigo de estado y municipios respectivamente
    Route::get('obtener/municipios/idEstado/{idEntidad}', 'obtenerMunicipiosPorEstado')->name('consMunicipiosXIdEstado');
    Route::get('obtener/asentamientos/idMunicipio/{idMunicipio}', 'obtenerAsentamientosPorMunicipio')->name('consAsentamientosXIdMunicipio');

    //Ruta para datos con codigo postal
    Route::get('obtener/datos/estado/municipio/asentamientos/{codigoPostal}', 'consDatosPorCodigoPostal')->name('consDatosXCodigoPostal');

    Route::get('obtener/datos/asentamiento/{idAsentamiento}', 'informacionAsentamiento')->name('infoAsentamiento');
});

Route::middleware([ServicioMiddleware::class])->group(function () {
    Route::controller(ServicioController::class)->group(function(){
    Route::get('/servicio', 'inicio')->name('servicio.inicio');
    Route::get('/servicio/perfil', 'perfil')->name('servicio.perfil');
    Route::put('/servicio/perfil/actualizar/contrasenia/{idUsuario}','actualizarContrasenia')->name('servicio.actualizarContrasenia');

    Route::get('/servicio/rutas', 'rutas')->name('servicio.rutas');
    Route::post('/servicio/rutas', 'addRuta')->name('servicio.addRuta');
    Route::put('/servicio/rutas/{idRuta}/edit','actualizarRuta')->name('servicio.actualizarRuta');
    Route::delete('/servicio/rutas/{rutasIds}', 'eliminarRuta')->name('servicio.eliminarRuta');

    Route::get('/servicio/formUnidades', 'formarUnidades')->name('servicio.formarUni');
    Route::post('/servicio/formarUnidades', 'registrarHoraEntrada')->name('servicio.registarHoraEntrada');
    Route::post('/servicio/formarUnidades/corte', 'registrarCorte')->name('servicio.registarCorte');
    Route::post('/servicio/formarUnidades/regreso', 'RegistrarHoraRegreso')->name('servicio.registrarHoraRegreso');
    Route::post('/servicio/formarUnidades/regUC', 'registrarUC')->name('servicio.registrarUC');
    Route::post('/servicio/formarUnidades/regresoUC', 'RegistrarHoraRegresoUC')->name('servicio.regresoUC');
    Route::post('/servicio/formarUnidades/regCastigo', 'registrarCastigo')->name('servicio.registrarCastigo');
    Route::post('/servicio/formarUnidades/finCastigo', 'RegistrarFinCastigo')->name('servicio.registrarFinCastigo');
    Route::post('/servicio/formarUnidades/trabajaDomingo', 'registrarTrabajanDomingo')->name('servicio.trabDomingo');

    Route::get('/servicio/sociosPrestadores', 'sociosPrestadores')->name('servicio.sociosPrestadores');
    Route::post('/servicio/sociosPrestadores', 'addDirectivo')->name('servicio.addDirectivo');
    Route::put('/servicio/sociosPrestadores/{idDirectivo}/edit', 'actualizarDirectivo')->name('servicio.actualizarDirectivo');
    Route::delete('/servicio/sociosPrestadores/{directivosIds}', 'eliminarDirectivo')->name('servicio.eliminarDirectivo');

    Route::get('/servicio/operadores', 'operadores')->name('servicio.operadores');
    Route::post('/servicio/operadores', 'addOperador')->name('servicio.addOperador');
    Route::put('/servicio/operadores/{idOperador}/edit', 'actualizarOperador')->name('servicio.actualizarOperador');
    Route::delete('/servicio/operadores/{operadoresIds}', 'eliminarOperador')->name('servicio.eliminarOperador');

    Route::get('/servicio/unidades', 'unidades')->name('servicio.unidades');
    Route::post('/servicio/unidades', 'addUnidad')->name('servicio.addUnidad');
    Route::put('/servicio/unidades/{idUnidad}/edit', 'actualizarUnidad')->name('servicio.actualizarUnidad');
    Route::delete('/servicio/unidades/{unidadesIds}', 'eliminarUnidad')->name('servicio.eliminarUnidad');
    Route::post('/servicio/unidades/asignarOper', 'asignarOperador')->name('servicio.asignarOperador');
    Route::post('/servicio/unidades/quitarOper', 'quitarOperador')->name('servicio.quitarOperador');

    Route::get('/servicio/reportes', 'reportes')->name('servicio.reportes');

    Route::post('/servicio/formarUnidades/rolServicio', 'cambiarRolServicio')->name('servicio.cambiarRolServicio');
    Route::put('/servicio/formarUnidades/rolRutas', 'actualizarRolRuta')->name('servicio.actualizarRolRuta');
    
    Route::get('/servicio/registros/{tipoRegistro}', 'obtenerRegistros')->name('servicio.obtenerRegistros');

    Route::put('/servicio/actualizarEntrada/{id}','actualizarEntrada')->name('servicio.actualizarEntrada');
    Route::put('/servicio/actualizarCorte/{id}','actualizarCorte')->name('servicio.actualizarCorte');
    Route::put('/servicio/actualizarCastigo/{id}','actualizarCastigo')->name('servicio.actualizarCastigo');
    Route::put('/servicio/actualizarUC/{id}','actualizarUltimaCorrida')->name('servicio.actualizarUltimaCorrida');

    Route::delete('/servicio/eliminarRegistro/{id}','eliminarRegistro')->name('servicio.eliminarRegistro');
    });

});

Route::middleware([RHMiddleware::class])->group(function () {
    Route::controller(RHController::class)->group(function(){
    Route::get('/RecursosHumanos', 'inicio')->name('rh.inicio');
    Route::get('/RecursosHumanos/perfil', 'perfil')->name('rh.perfil');
    Route::put('/RecursosHumanos/perfil/actualizar/contrasenia/{idUsuario}','actualizarContrasenia')->name('rh.actualizarContrasenia');

    Route::get('/RecursosHumanos/operadores', 'operadores')->name('rh.operadores');
    Route::post('/RecursosHumanos/operadores/agregar', 'addOperador')->name('rh.addOperador');
    Route::put('/RecursosHumanos/operadores/{idOperador}/edit', 'actualizarOperador')->name('rh.actualizarOperador');
    Route::delete('/RecursosHumanos/operadores/{operadoresIds}', 'eliminarOperador')->name('rh.eliminarOperador');

    Route::get('/RecursosHumanos/formUnidades', 'formarUnidades')->name('rh.formarUnidades');

    Route::get('/RecursosHumanos/sociosPrestadores', 'sociosPrestadores')->name('rh.sociosPrestadores');
    Route::post('/RecursosHumanos/sociosPrestadores', 'addDirectivo')->name('rh.addDirectivo');
    Route::put('/RecursosHumanos/sociosPrestadores/{idDirectivo}/edit', 'actualizarDirectivo')->name('rh.actualizarDirectivo');
    Route::delete('/RecursosHumanos/sociosPrestadores/{directivosIds}', 'eliminarDirectivo')->name('rh.eliminarDirectivo');

    Route::get('/RecursosHumanos/incapacidades', 'incapacidades')->name('rh.incapacidades');
    Route::post('/RecursosHumanos/incapacidades', 'addIncapacidad')->name('rh.addIncapacidad');
    Route::put('/RecursosHumanos/incapacidades/{idIncapacidad}/edit', 'actualizarIncapacidad')->name('rh.actualizarIncapacidad');
    Route::delete('/RecursosHumanos/incapacidades/{incapacidadesIds}', 'eliminarIncapacidad')->name('rh.eliminarIncapacidad');
    Route::put('/RecursosHumanos/incapacidades/reincorporar', 'reincorporarOperador')->name('rh.reincorporar');

    Route::get('/RecursosHumanos/personalAdministrativo', 'personalAdministrativo')->name('rh.personalAdministrativo');
    Route::post('/RecursosHumanos/personalAdministrativo/agregar', 'addPersonal')->name('rh.addPersonal');
    Route::put('/RecursosHumanos/personal/{idPersonal}/edit', 'actualizarPersonal')->name('rh.actualizarPersonal');
    Route::delete('/RecursosHumanos/personal/{personalesIds}', 'eliminarPersonal')->name('rh.eliminarPersonal');

    Route::get('/RecursosHumanos/movimientos', 'movimientos')->name('rh.movimientos');
    Route::post('/RecursosHumanos/agregarMovimiento', 'addMovimiento')->name('rh.addMovimiento');
    Route::delete('/RecursosHumanos/movimientos/{movimientosIds}', 'eliminarMovimiento')->name('rh.eliminarMovimiento');

    Route::get('/RecursosHumanos/reportes', 'reportes')->name('rh.reportes');

    Route::get('/RecursosHumanos/vacaciones', 'vacaciones')->name('rh.vacaciones');
    Route::post('/RecursosHumanos/agregarVacaciones', 'addVacaciones')->name('rh.addVacaciones');
    Route::delete('/RecursosHumanos/eliminarVacaciones/{vacacionesIds}', 'eliminarVacaciones')->name('rh.eliminarVacaciones');
    });
});

Route::middleware([DirectivoMiddleware::class])->group(function () {
    Route::controller(DirectivoController::class)->group(function(){
    Route::get('/Directivo', 'inicio')->name('directivo.inicio');
    Route::get('/Directivo/perfil', 'perfil')->name('directivo.perfil');
    Route::put('/Directivo/perfil/actualizar/contrasenia/{idUsuario}','actualizarContrasenia')->name('directivo.actualizarContrasenia');

    Route::get('/Directivo/formUnidades', 'formarUnidades')->name('directivo.formarUnidades');
    });
});