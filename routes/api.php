<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Registrar usuarios
Route::post('/register', [UserController::class, 'register']);

//Iniciar sesion
Route::post('/login', [UserController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    // Obtener todos los usuarios
    Route::get('/user', [UserController::class, 'index']);
    
    // Actualizar usuario
    Route::put('/update/{id}', [UserController::class, 'update']);

    // Eliminar usuario
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);

});