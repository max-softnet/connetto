<?php

use App\Http\Controllers\Api\AppuntamentoController;
use App\Http\Controllers\Api\FileMakerImportController;
use App\Http\Controllers\Api\MessaggioController;
use App\Http\Controllers\Api\ModelloMessaggioController;
use App\Http\Controllers\Api\OperatoreController;
use App\Http\Controllers\Api\TipoAppuntamentoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::apiResource('tipi-appuntamento', TipoAppuntamentoController::class)
        ->parameters(['tipi-appuntamento' => 'tipoAppuntamento']);

    Route::apiResource('operatori', OperatoreController::class)
        ->parameters(['operatori' => 'operatore']);

    Route::apiResource('appuntamenti', AppuntamentoController::class)
        ->parameters(['appuntamenti' => 'appuntamento']);

    Route::apiResource('modelli-messaggio', ModelloMessaggioController::class)
        ->parameters(['modelli-messaggio' => 'modelloMessaggio']);

    Route::apiResource('messaggi', MessaggioController::class)
        ->parameters(['messaggi' => 'messaggio'])
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('filemaker/appuntamenti', [FileMakerImportController::class, 'store'])
        ->name('filemaker.appuntamenti.importa');
});
