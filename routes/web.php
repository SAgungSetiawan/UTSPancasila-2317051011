<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'menu'])->name('game.menu');
Route::get('/game', [GameController::class, 'main'])->name('game.main');
Route::post('/game/simpan-hasil', [GameController::class, 'simpanHasil'])->name('game.simpanHasil');
Route::get('/game/hasil', [GameController::class, 'hasil'])->name('game.hasil');
