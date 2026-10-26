<?php

use App\Http\Controllers\LigaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'liga');

Route::prefix('api')->group(function () {
    Route::get('/estado', [LigaController::class, 'estado']);
    Route::post('/equipos', [LigaController::class, 'crearEquipo']);
    Route::patch('/equipos/{id}', [LigaController::class, 'editarEquipo']);
    Route::delete('/equipos/{id}', [LigaController::class, 'eliminarEquipo']);
    Route::post('/jugadores', [LigaController::class, 'crearJugador']);
    Route::patch('/jugadores/{id}', [LigaController::class, 'editarJugador']);
    Route::delete('/jugadores/{id}', [LigaController::class, 'eliminarJugador']);
    Route::post('/partidos', [LigaController::class, 'crearPartido']);
    Route::patch('/partidos/{id}', [LigaController::class, 'editarPartido']);
    Route::delete('/partidos/{id}', [LigaController::class, 'eliminarPartido']);
    Route::get('/exportar', [LigaController::class, 'exportar']);
    Route::post('/importar', [LigaController::class, 'importar']);
});
