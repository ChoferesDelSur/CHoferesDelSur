<?php

use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;

//Route::get('/', [PrincipalController::class, 'inicio'])->name('inicio');
//Route::get('/', [PrincipalController::class, 'formarUnidades'])->name('formarUnidades');
Route::controller(PrincipalController::class)->group(function () {
    Route::get('/', 'inicio')->name('principal.inicio');
    Route::get('/principal', 'formarUnidades')->name('principal.formarUnidades');
});