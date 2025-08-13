<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Página de inicio
Route::get('/', function () {
    return view('welcome');
});

// Login (mostrar formulario)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Procesar login
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CRUD de usuarios (solo si está autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'bladeIndex'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'bladeCreate'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'bladeEdit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
});
