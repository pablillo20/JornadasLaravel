<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ponente API

Route::get('/ponentes',[PonenteController::class, 'obtenerPonentes']);

Route::delete('/ponentes/{id}', [PonenteController::class, 'borrarPonentes']);

Route::post('/ponentes', [PonenteController::class, 'crearPonente']);

Route::put('/ponentes/{id}', [PonenteController::class, 'editarPonente']);

// EVENTO API

Route::get('/eventos',[EventoController::class, 'obtenerEvento']);

Route::delete('/eventos/{id}', [EventoController::class, 'borrarEvento']);

Route::post('/eventos', [EventoController::class, 'crearEvento']);

// INSCRIPCION API

Route::get('/inscripcion',[InscripcionController::class, 'obtenerInscripcion']);

Route::delete('/inscripcion/{id}', [InscripcionController::class, 'borrarInscripcion']);

Route::post('/inscripcion', [InscripcionController::class, 'crearInscripcion']);

Route::get('/inscripcion/{user_id}/{evento_id}', [InscripcionController::class, 'verificarInscripcion']);

// PAGOS API

Route::get('/pago',[PagoController::class, 'obtenerPagos']);

// USUARIO API

Route::get('/usuarios', [UserController::class, 'ObtenerUser']);

Route::patch('/usuario/{userId}/actualizar-estudiante', [UserController::class, 'actualizarEsEstudiante']);

