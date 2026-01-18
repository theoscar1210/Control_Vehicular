<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Alerta;
use App\Models\Usuario;
use Carbon\Carbon;

class EnviarAlertasSemanales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:enviar-semanales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía resumen semanal de alertas por correo electrónico todos los lunes a las 01:00 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envío de alertas semanales...');

        // Obtener todas las alertas no leídas
        $alertas = Alerta::where('leida', 0)
            ->whereNull('deleted_at')
            ->orderBy('tipo_vencimiento')
            ->orderByDesc('fecha_alerta')
            ->get();

        if ($alertas->isEmpty()) {
            $this->info('No hay alertas pendientes para enviar.');
            return 0;
        }

        // Agrupar alertas por tipo de vencimiento
        $alertasPorTipo = $alertas->groupBy('tipo_vencimiento');

        // Obtener usuarios ADMIN y SST activos para enviar correos
        $destinatarios = Usuario::whereIn('rol', ['ADMIN', 'SST'])
            ->where('activo', 1)
            ->whereNotNull('email')
            ->get();

        if ($destinatarios->isEmpty()) {
            $this->warn('No hay destinatarios activos con correo configurado.');
            return 0;
        }

        // Preparar datos del correo
        $fecha = Carbon::now()->format('d/m/Y H:i');
        $totalAlertas = $alertas->count();

        // Enviar correo a cada destinatario
        foreach ($destinatarios as $usuario) {
            try {
                Mail::send('emails.alertas-semanales', [
                    'usuario' => $usuario,
                    'alertasPorTipo' => $alertasPorTipo,
                    'totalAlertas' => $totalAlertas,
                    'fecha' => $fecha
                ], function ($message) use ($usuario) {
                    $message->to($usuario->email, $usuario->nombre)
                        ->subject('Resumen Semanal de Alertas - Control Vehicular');
                });

                $this->info("✓ Correo enviado a: {$usuario->email}");
            } catch (\Exception $e) {
                $this->error("✗ Error enviando correo a {$usuario->email}: " . $e->getMessage());
            }
        }

        $this->info("Proceso completado. Total alertas: {$totalAlertas}");
        return 0;
    }
}
