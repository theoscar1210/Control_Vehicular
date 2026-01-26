<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */


    protected function schedule(Schedule $schedule)
    {
        // Verificación diaria de documentos vencidos a las 03:00 AM
        $schedule->command('check:document-expirations')->dailyAt('03:00');

        // Envío semanal de alertas cada Lunes a las 04:00 AM
        // Usa la hora de la red (timezone del servidor configurado en config/app.php)
        $schedule->command('alertas:enviar-semanales')
            ->weeklyOn(1, '04:00') // 1 = Lunes
            ->timezone(config('app.timezone')); // Usa timezone de la red
    }
}
