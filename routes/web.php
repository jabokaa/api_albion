<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CraftController;
use App\Http\Controllers\TransportController;

use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('welcome');
});


// Rotas para CraftController
Route::get('/craft/{dataType}', [CraftController::class, 'index'])->name('craft.index');
Route::get('/craft/details/{dataType}', [CraftController::class, 'details'])->name('craft.details');

// Rotas para TransportDayController
Route::get('/transport/{dataType}', [TransportController::class, 'index'])->name('transport.index');
Route::get('/transport/details/{dataType}', [TransportController::class, 'show'])->name('transport.details');
Route::get('/transport/show-item/{dataType}/{name}', [TransportController::class, 'showItem'])->name('transport.showItem');

// Rotas para ItemController
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/details', [ItemController::class, 'details'])->name('items.details');
