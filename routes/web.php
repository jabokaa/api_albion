<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CraftController;
use App\Http\Controllers\TransportDayController;

Route::get('/', function () {
    return view('welcome');
});


// Rotas para CraftController
Route::get('/craft', [CraftController::class, 'index'])->name('craft.index');
Route::get('/craft/details', [CraftController::class, 'details'])->name('craft.details');

// Rotas para TransportDayController
Route::get('/transport-day', [TransportDayController::class, 'index'])->name('transport_day.index');
