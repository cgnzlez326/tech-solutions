<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/registro', [AuthController::class, 'registro']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('web')->group(function () {
    Route::get('/proyectos', [ProyectoController::class, 'index']);
    Route::post('/proyectos', [ProyectoController::class, 'store']);
    Route::get('/proyectos/{id}', [ProyectoController::class, 'show']);
    Route::put('/proyectos/{id}', [ProyectoController::class, 'update']);
    Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy']);
});
