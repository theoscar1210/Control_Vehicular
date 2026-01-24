<?php

namespace App\Console\Commands;

use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Propietario;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgarRegistrosEliminados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registros:purgar
                            {--meses=6 : Número de meses después de los cuales se eliminan definitivamente}
                            {--dry-run : Simular sin eliminar realmente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar permanentemente vehículos, conductores y propietarios que fueron eliminados hace más de 6 meses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meses = (int) $this->option('meses');
        $dryRun = $this->option('dry-run');
        $fechaLimite = Carbon::now()->subMonths($meses);

        $this->info("===========================================");
        $this->info(" PURGA DE REGISTROS ELIMINADOS");
        $this->info("===========================================");
        $this->info("Fecha límite: {$fechaLimite->format('d/m/Y H:i:s')}");
        $this->info("Modo: " . ($dryRun ? "SIMULACIÓN (dry-run)" : "EJECUCIÓN REAL"));
        $this->newLine();

        $totalVehiculos = 0;
        $totalConductores = 0;
        $totalPropietarios = 0;
        $totalDocumentosVehiculo = 0;
        $totalDocumentosConductor = 0;

        try {
            DB::transaction(function () use (
                $fechaLimite,
                $dryRun,
                &$totalVehiculos,
                &$totalConductores,
                &$totalPropietarios,
                &$totalDocumentosVehiculo,
                &$totalDocumentosConductor
            ) {
                // =========================================
                // 1. VEHÍCULOS Y SUS DOCUMENTOS
                // =========================================
                $this->info("1. Procesando VEHÍCULOS...");

                $vehiculosParaEliminar = Vehiculo::onlyTrashed()
                    ->where('deleted_at', '<', $fechaLimite)
                    ->get();

                foreach ($vehiculosParaEliminar as $vehiculo) {
                    // Contar documentos del vehículo
                    $docsVehiculo = DocumentoVehiculo::withTrashed()
                        ->where('id_vehiculo', $vehiculo->id_vehiculo)
                        ->count();

                    $totalDocumentosVehiculo += $docsVehiculo;

                    $this->line("   → Vehículo: {$vehiculo->placa} (Eliminado: {$vehiculo->deleted_at->format('d/m/Y')}) - {$docsVehiculo} documentos");

                    if (!$dryRun) {
                        // Eliminar documentos del vehículo permanentemente
                        DocumentoVehiculo::withTrashed()
                            ->where('id_vehiculo', $vehiculo->id_vehiculo)
                            ->forceDelete();

                        // Eliminar vehículo permanentemente
                        $vehiculo->forceDelete();
                    }

                    $totalVehiculos++;
                }

                $this->info("   Total vehículos: {$totalVehiculos}");
                $this->info("   Total documentos vehículo: {$totalDocumentosVehiculo}");
                $this->newLine();

                // =========================================
                // 2. CONDUCTORES Y SUS DOCUMENTOS
                // =========================================
                $this->info("2. Procesando CONDUCTORES...");

                $conductoresParaEliminar = Conductor::onlyTrashed()
                    ->where('deleted_at', '<', $fechaLimite)
                    ->get();

                foreach ($conductoresParaEliminar as $conductor) {
                    // Contar documentos del conductor
                    $docsConductor = DocumentoConductor::withTrashed()
                        ->where('id_conductor', $conductor->id_conductor)
                        ->count();

                    $totalDocumentosConductor += $docsConductor;

                    $this->line("   → Conductor: {$conductor->nombre} {$conductor->apellido} (Eliminado: {$conductor->deleted_at->format('d/m/Y')}) - {$docsConductor} documentos");

                    if (!$dryRun) {
                        // Eliminar documentos del conductor permanentemente
                        DocumentoConductor::withTrashed()
                            ->where('id_conductor', $conductor->id_conductor)
                            ->forceDelete();

                        // Eliminar conductor permanentemente
                        $conductor->forceDelete();
                    }

                    $totalConductores++;
                }

                $this->info("   Total conductores: {$totalConductores}");
                $this->info("   Total documentos conductor: {$totalDocumentosConductor}");
                $this->newLine();

                // =========================================
                // 3. PROPIETARIOS HUÉRFANOS
                // =========================================
                $this->info("3. Procesando PROPIETARIOS huérfanos...");

                // Propietarios eliminados que ya no tienen vehículos
                $propietariosParaEliminar = Propietario::onlyTrashed()
                    ->where('deleted_at', '<', $fechaLimite)
                    ->whereDoesntHave('vehiculos')
                    ->get();

                foreach ($propietariosParaEliminar as $propietario) {
                    $this->line("   → Propietario: {$propietario->nombre} {$propietario->apellido} (Eliminado: {$propietario->deleted_at->format('d/m/Y')})");

                    if (!$dryRun) {
                        $propietario->forceDelete();
                    }

                    $totalPropietarios++;
                }

                $this->info("   Total propietarios: {$totalPropietarios}");
                $this->newLine();

                // Si es dry-run, hacer rollback
                if ($dryRun) {
                    throw new \Exception('DRY_RUN_ROLLBACK');
                }
            });
        } catch (\Exception $e) {
            if ($e->getMessage() !== 'DRY_RUN_ROLLBACK') {
                $this->error("Error durante la purga: " . $e->getMessage());
                Log::error("Error en PurgarRegistrosEliminados: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // =========================================
        // RESUMEN FINAL
        // =========================================
        $this->info("===========================================");
        $this->info(" RESUMEN DE PURGA");
        $this->info("===========================================");
        $this->table(
            ['Tipo', 'Cantidad'],
            [
                ['Vehículos', $totalVehiculos],
                ['Documentos de Vehículo', $totalDocumentosVehiculo],
                ['Conductores', $totalConductores],
                ['Documentos de Conductor', $totalDocumentosConductor],
                ['Propietarios', $totalPropietarios],
            ]
        );

        $totalRegistros = $totalVehiculos + $totalConductores + $totalPropietarios + $totalDocumentosVehiculo + $totalDocumentosConductor;

        if ($dryRun) {
            $this->warn("SIMULACIÓN: Se habrían eliminado {$totalRegistros} registros en total.");
            $this->warn("Ejecute sin --dry-run para eliminar definitivamente.");
        } else {
            $this->info("Se eliminaron permanentemente {$totalRegistros} registros.");

            // Log de la operación
            Log::info("PurgarRegistrosEliminados: Eliminados {$totalVehiculos} vehículos, {$totalConductores} conductores, {$totalPropietarios} propietarios, {$totalDocumentosVehiculo} docs vehículo, {$totalDocumentosConductor} docs conductor");
        }

        return Command::SUCCESS;
    }
}
