<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Alerta;
use App\Models\Usuario;
use App\Notifications\DocumentoVencidoNotification;
use Carbon\Carbon;
use DB;

class CheckDocumentExpirations extends Command
{
    /**
     *El nombre y la firma del comando de la consola.
     *
     * @var string
     */
    protected $signature = 'check:document-expirations';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Revisa documentos próximos a vencer y vencidos, crea alertas y nitifica.';

    /**
     * Ejecuta el comando de la consola.
     */
    public function handle()
    {
        $hoy = Carbon::today();
        $proximo = $hoy->copy()->addDays(15);
        $creadas = 0;
        $documentosRevisados = 0;

        // =====================================================
        // 1. Revisar documentos de VEHÍCULOS (comportamiento original)
        // =====================================================
        $docsV = DocumentoVehiculo::whereNull('reemplazado_por')
            ->where('estado', '!=', 'REEMPLAZADO')
            ->where(function ($q) use ($hoy, $proximo) {
                $q->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                    ->orWhere('fecha_vencimiento', '<', $hoy);
            })->get();

        foreach ($docsV as $d) {
            $documentosRevisados++;
            $tipo_v = $d->fecha_vencimiento < $hoy ? 'VENCIDO' : 'PROXIMO_VENCER';

            // Verificar si ya existe una alerta activa
            $alertaExistente = Alerta::where('tipo_vencimiento', $tipo_v)
                ->whereNull('deleted_at')
                ->where('id_doc_vehiculo', $d->id_doc_vehiculo)
                ->exists();

            if ($alertaExistente) {
                continue;
            }

            $mensaje = sprintf("Documento %s (%s) - vence: %s",
                $d->tipo_documento,
                $d->numero_documento,
                $d->fecha_vencimiento ? $d->fecha_vencimiento->format('Y-m-d') : 'Sin fecha'
            );

            Alerta::create([
                'tipo_alerta' => 'VEHICULO',
                'id_doc_vehiculo' => $d->id_doc_vehiculo,
                'id_doc_conductor' => null,
                'tipo_vencimiento' => $tipo_v,
                'mensaje' => $mensaje,
                'fecha_alerta' => Carbon::today(),
                'leida' => 0,
                'visible_para' => 'TODOS',
                'creado_por' => null,
            ]);
            $creadas++;
        }

        // =====================================================
        // 2. Revisar documentos de CONDUCTORES
        //    Para licencias: generar alertas por categoría monitoreada
        // =====================================================
        $docsC = DocumentoConductor::with('conductor')
            ->whereNull('reemplazado_por')
            ->where('activo', 1)
            ->get();

        foreach ($docsC as $doc) {
            $documentosRevisados++;

            // Si es una licencia de conducción, verificar por categoría
            if ($doc->tipo_documento === 'Licencia Conducción' && !empty($doc->fechas_por_categoria)) {
                $creadas += $this->procesarLicenciaPorCategoria($doc, $hoy, $proximo);
            } else {
                // Para otros documentos (EPS, ARL, etc.), usar comportamiento original
                $creadas += $this->procesarDocumentoSimple($doc, $hoy, $proximo);
            }
        }

        $this->info("Check completed - documents checked: {$documentosRevisados}, alerts created: {$creadas}");
        return 0;
    }

    /**
     * Procesar licencia de conducción por categorías monitoreadas.
     * Genera alertas individuales para cada categoría que esté próxima a vencer.
     */
    private function procesarLicenciaPorCategoria(DocumentoConductor $doc, Carbon $hoy, Carbon $proximo): int
    {
        $creadas = 0;
        $categoriasAMonitorear = $doc->getCategoriasAMonitorear();
        $fechasPorCategoria = $doc->fechas_por_categoria;

        foreach ($categoriasAMonitorear as $categoria) {
            // Obtener fecha de vencimiento de esta categoría
            $fechaVencimiento = null;
            if (isset($fechasPorCategoria[$categoria]['fecha_vencimiento'])) {
                $fechaVencimiento = Carbon::parse($fechasPorCategoria[$categoria]['fecha_vencimiento']);
            }

            // Si no hay fecha para esta categoría, saltar
            if (!$fechaVencimiento) {
                continue;
            }

            // Verificar si está vencida o próxima a vencer
            $necesitaAlerta = $fechaVencimiento->lt($hoy) ||
                ($fechaVencimiento->gte($hoy) && $fechaVencimiento->lte($proximo));

            if (!$necesitaAlerta) {
                continue;
            }

            $tipo_v = $fechaVencimiento->lt($hoy) ? 'VENCIDO' : 'PROXIMO_VENCER';

            // Crear mensaje único que incluye la categoría
            $mensajeBusqueda = "Licencia categoría {$categoria}";

            // Verificar si ya existe una alerta para esta categoría específica
            $alertaExistente = Alerta::where('tipo_vencimiento', $tipo_v)
                ->whereNull('deleted_at')
                ->where('id_doc_conductor', $doc->id_doc_conductor)
                ->where('mensaje', 'like', "%{$mensajeBusqueda}%")
                ->exists();

            if ($alertaExistente) {
                continue;
            }

            // Obtener nombre del conductor
            $nombreConductor = $doc->conductor
                ? "{$doc->conductor->nombre} {$doc->conductor->apellido}"
                : 'Conductor desconocido';

            $mensaje = sprintf("Licencia categoría %s (%s) - %s - vence: %s",
                $categoria,
                $doc->numero_documento,
                $nombreConductor,
                $fechaVencimiento->format('Y-m-d')
            );

            Alerta::create([
                'tipo_alerta' => 'CONDUCTOR',
                'id_doc_vehiculo' => null,
                'id_doc_conductor' => $doc->id_doc_conductor,
                'tipo_vencimiento' => $tipo_v,
                'mensaje' => $mensaje,
                'fecha_alerta' => Carbon::today(),
                'leida' => 0,
                'visible_para' => 'TODOS',
                'creado_por' => null,
            ]);
            $creadas++;
        }

        return $creadas;
    }

    /**
     * Procesar documento simple (no licencia o licencia sin fechas por categoría).
     * Usa el comportamiento original basado en fecha_vencimiento del documento.
     */
    private function procesarDocumentoSimple(DocumentoConductor $doc, Carbon $hoy, Carbon $proximo): int
    {
        if (!$doc->fecha_vencimiento) {
            return 0;
        }

        $fechaVencimiento = Carbon::parse($doc->fecha_vencimiento);

        // Verificar si está vencida o próxima a vencer
        $necesitaAlerta = $fechaVencimiento->lt($hoy) ||
            ($fechaVencimiento->gte($hoy) && $fechaVencimiento->lte($proximo));

        if (!$necesitaAlerta) {
            return 0;
        }

        $tipo_v = $fechaVencimiento->lt($hoy) ? 'VENCIDO' : 'PROXIMO_VENCER';

        // Verificar si ya existe una alerta
        $alertaExistente = Alerta::where('tipo_vencimiento', $tipo_v)
            ->whereNull('deleted_at')
            ->where('id_doc_conductor', $doc->id_doc_conductor)
            ->exists();

        if ($alertaExistente) {
            return 0;
        }

        $mensaje = sprintf("Documento %s (%s) - vence: %s",
            $doc->tipo_documento,
            $doc->numero_documento,
            $fechaVencimiento->format('Y-m-d')
        );

        Alerta::create([
            'tipo_alerta' => 'CONDUCTOR',
            'id_doc_vehiculo' => null,
            'id_doc_conductor' => $doc->id_doc_conductor,
            'tipo_vencimiento' => $tipo_v,
            'mensaje' => $mensaje,
            'fecha_alerta' => Carbon::today(),
            'leida' => 0,
            'visible_para' => 'TODOS',
            'creado_por' => null,
        ]);

        return 1;
    }
}
