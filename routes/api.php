<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SincronizacaoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sincronizar/produtos', [SincronizacaoController::class, 'sincronizarProdutos']);
Route::post('/sincronizar/precos', [SincronizacaoController::class, 'sincronizarPrecos']);
Route::get('/produtos-precos', [SincronizacaoController::class, 'listarProdutos']);