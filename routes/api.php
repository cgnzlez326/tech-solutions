<?php

use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::get('/proyectos', [ProyectoController::class, 'index']);
Route::post('/proyectos', [ProyectoController::class, 'store']);
Route::get('/proyectos/{id}', [ProyectoController::class, 'show']);
Route::put('/proyectos/{id}', [ProyectoController::class, 'update']);
Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy']);
