<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGameController;


Route::post('/buat-pemain', [ApiGameController::class, 'buatPemain']);
Route::post('/update-skor', [ApiGameController::class, 'updateSkor']);
Route::post('/ambil-status', [ApiGameController::class, 'ambilStatus']);
Route::post('/reset', [ApiGameController::class, 'reset']);