<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HiscoreController;

//Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para la página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/hiscores', [HiscoreController::class, 'store'])->name('hiscores.store');

// Ruta para actualizar un High Score
Route::put('/hiscores/{id}', [HiscoreController::class, 'updateName']);

