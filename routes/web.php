<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CraftController;
use App\Http\Controllers\TransportController;

Route::get('/', function () {
    return view('welcome');
});


// Rotas para CraftController
Route::get('/craft', [CraftController::class, 'index'])->name('craft.index');
Route::get('/craft/details', [CraftController::class, 'details'])->name('craft.details');

// Rotas para TransportDayController
Route::get('/transport/{dataType}', [TransportController::class, 'index'])->name('transport.index');
