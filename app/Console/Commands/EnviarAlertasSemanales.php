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
     * El nombre y la firma del comando de la consola.
     *
     * @var string
     */
    protected $signature = 'alertas:enviar-semanales';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Envía resumen semanal de alertas pendientes (documentos no renovados) por correo electrónico';

    /**
     * Ejecuta el comando de la consola.
     */
    public function handle()
    {
        $this->info('Iniciando envío de alertas semanales...');

        // Obtener todas las alertas activas (documentos no renovados)
        // Se envían hasta que el documento sea renovado (solucionada = true)
        $alertas = Alerta::with([
            'documentoVehiculo.vehiculo.conductor',
            'documentoConductor.conductor'
        ])
            ->activas() // Solo alertas no solucionadas (documento no renovado)
            ->whereNull('deleted_at')
            ->orderBy('tipo_vencimiento')
            ->orderByDesc('fecha_alerta')
            ->get();

        if ($alertas->isEmpty()) {
            $this->info('No hay alertas pendientes. Todos los documentos están al día.');
            return 0;
        }

        // Agrupar alertas por tipo de vencimiento
        $alertasPorTipo = $alertas->groupBy('tipo_vencimiento');

        // Obtener usuarios ADMIN y SST PORTERIA activos para enviar correos
        $destinatarios = Usuario::whereIn('rol', ['ADMIN', 'SST', 'PORTERIA'])
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
