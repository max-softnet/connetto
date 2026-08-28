<?php

use App\Http\Controllers\AutomazioneWebController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarioWebController;
use App\Http\Controllers\HomeWebController;
use App\Http\Controllers\ImpostazioniWebController;
use App\Http\Controllers\LogWhatsappWebController;
use App\Http\Controllers\MessaggioWebController;
use App\Http\Controllers\ModelloMessaggioWebController;
use App\Http\Controllers\UtenteWebController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostraLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.tentativo');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [HomeWebController::class, 'index'])->name('home');

    Route::get('/calendario', [CalendarioWebController::class, 'giorno'])->name('calendario.giorno');
    Route::get('/calendario/elenco', [CalendarioWebController::class, 'elenco'])->name('calendario.elenco');
    Route::get('/calendario/mese', [CalendarioWebController::class, 'mese'])->name('calendario.mese');

    Route::middleware('ruolo:admin')->group(function () {
        Route::get('/appuntamenti/{appuntamento}/messaggi/crea', [MessaggioWebController::class, 'crea'])->name('messaggi.crea');
        Route::post('/appuntamenti/{appuntamento}/messaggi', [MessaggioWebController::class, 'salva'])->name('messaggi.salva');

        Route::resource('modelli-messaggio', ModelloMessaggioWebController::class)
            ->except('show')
            ->parameters(['modelli-messaggio' => 'modelloMessaggio']);

        Route::get('/messaggi', [MessaggioWebController::class, 'index'])->name('messaggi.index');
        Route::post('/messaggi/{messaggio}/invia', [MessaggioWebController::class, 'invia'])->name('messaggi.invia');
        Route::delete('/messaggi/{messaggio}', [MessaggioWebController::class, 'destroy'])->name('messaggi.destroy');

        Route::resource('utenti', UtenteWebController::class)
            ->except('show')
            ->parameters(['utenti' => 'utente']);

        Route::get('/impostazioni', [ImpostazioniWebController::class, 'mostra'])->name('impostazioni.mostra');
        Route::post('/impostazioni', [ImpostazioniWebController::class, 'salva'])->name('impostazioni.salva');

        Route::get('/log-whatsapp', [LogWhatsappWebController::class, 'index'])->name('log-whatsapp.index');

        Route::resource('automazioni', AutomazioneWebController::class)
            ->except('show')
            ->parameters(['automazioni' => 'automazione']);

        Route::post('/automazioni/esegui', [AutomazioneWebController::class, 'esegui'])->name('automazioni.esegui');
    });
});
