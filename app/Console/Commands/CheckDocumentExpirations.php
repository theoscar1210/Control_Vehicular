<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlertaService;

class CheckDocumentExpirations extends Command
{
    /**
     * El nombre y la firma del comando de la consola.
     */
    protected $signature = 'check:document-expirations';

    /**
     * La descripción del comando de consola.
     */
    protected $description = 'Revisa documentos próximos a vencer y vencidos, crea alertas y notifica.';

    /**
     * Ejecuta el comando de la consola.
     */
    public function handle(AlertaService $alertaService)
    {
        $this->info('Verificando documentos de vehículos...');
        $resultadoVehiculos = $alertaService->procesarDocumentosVehiculosBatch();

        $this->info('Verificando documentos de conductores...');
        $resultadoConductores = $alertaService->procesarDocumentosConductoresBatch();

        $totalRevisados = $resultadoVehiculos['revisados'] + $resultadoConductores['revisados'];
        $totalCreadas = $resultadoVehiculos['creadas'] + $resultadoConductores['creadas'];

        $this->info("Check completed - documents checked: {$totalRevisados}, alerts created: {$totalCreadas}");
        return 0;
    }
}
