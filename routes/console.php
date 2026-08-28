<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Richiede un cron reale sul server che lanci "php artisan schedule:run" ogni minuto
// (`* * * * * php artisan schedule:run`). In locale va lanciata a mano con
// `php artisan automazioni:esegui` per i test.
Schedule::command('automazioni:esegui')->dailyAt('08:00');
