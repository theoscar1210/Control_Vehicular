<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| TAREAS PROGRAMADAS
|--------------------------------------------------------------------------
*/

// Verificar vencimiento de documentos (diariamente a las 3:00 AM)
Schedule::command('check:document-expirations')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/check-expirations.log'));

// Enviar alertas semanales (lunes a las 4:00 AM)
Schedule::command('alertas:enviar-semanales')
    ->weeklyOn(1, '04:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/alertas-semanales.log'));

// Backup diario de base de datos (2:00 AM)
Schedule::command('backup:database')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup.log'));

// Purgar registros eliminados hace más de 6 meses (diariamente a las 2:30 AM)
Schedule::command('registros:purgar')
    ->dailyAt('02:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/purgar-registros.log'));
