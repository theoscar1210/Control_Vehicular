<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Carbon\Carbon;

/**
 * Servicio centralizado para la generacion y gestion de alertas.
 *
 * Responsabilidades:
 * - Evaluar si un documento requiere alerta (vencido o proximo a vencer)
 * - Crear alertas evitando duplicados
 * - Solucionar alertas al renovar documentos
 * - Procesar lotes de documentos (usado por el comando scheduled)
 */
class AlertaService
{
    /** Dias de anticipacion para alertas de proximidad a vencer */
    private const DIAS_ANTICIPACION = 15;

    /**
     * Evaluar y generar alerta para un documento de vehiculo.
     * Se llama al crear o renovar un documento.
     *
     * @return Alerta|null La alerta creada, o null si no aplica
     */
    public function evaluarDocumentoVehiculo(DocumentoVehiculo $documento): ?Alerta
    {
        if (!$documento->fecha_vencimiento) {
            return null;
        }

        $evaluacion = $this->evaluarVigencia($documento->fecha_vencimiento);

        if (!$evaluacion['requiere_alerta']) {
            return null;
        }

        // Verificar duplicado
        if ($this->existeAlertaVehiculo($documento->id_doc_vehiculo, $evaluacion['tipo'])) {
            return null;
        }

        return Alerta::create([
            'tipo_alerta'      => 'VEHICULO',
            'id_doc_vehiculo'  => $documento->id_doc_vehiculo,
            'id_doc_conductor' => null,
            'tipo_vencimiento' => $evaluacion['tipo'],
            'mensaje'          => sprintf(
                'Documento %s (%s) vence el %s',
                $documento->tipo_documento,
                $documento->numero_documento,
                Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y')
            ),
            'fecha_alerta'     => Carbon::today(),
            'leida'            => 0,
            'solucionada'      => false,
            'visible_para'     => 'TODOS',
            'creado_por'       => auth()->id() ?? null,
        ]);
    }

    /**
     * Evaluar y generar alertas para un documento de conductor.
     * Soporta licencias con fechas por categoria.
     *
     * @return int Numero de alertas creadas
     */
    public function evaluarDocumentoConductor(DocumentoConductor $documento): int
    {
        if ($documento->tipo_documento === 'Licencia Conducción' && !empty($documento->fechas_por_categoria)) {
            return $this->evaluarLicenciaPorCategoria($documento);
        }

        return $this->evaluarDocumentoConductorSimple($documento);
    }

    /**
     * Solucionar alertas de un documento de vehiculo al ser renovado.
     */
    public function solucionarPorRenovacionVehiculo(int $idDocVehiculo): int
    {
        return Alerta::solucionarPorDocumentoVehiculo($idDocVehiculo, 'DOCUMENTO_RENOVADO');
    }

    /**
     * Solucionar alertas de un documento de conductor al ser renovado.
     */
    public function solucionarPorRenovacionConductor(int $idDocConductor): int
    {
        return Alerta::solucionarPorDocumentoConductor($idDocConductor, 'DOCUMENTO_RENOVADO');
    }

    /**
     * Procesar todos los documentos de vehiculos pendientes (batch).
     * Usado por el comando check:document-expirations.
     *
     * @return array ['revisados' => int, 'creadas' => int]
     */
    public function procesarDocumentosVehiculosBatch(): array
    {
        $hoy = Carbon::today();
        $proximo = $hoy->copy()->addDays(self::DIAS_ANTICIPACION);
        $creadas = 0;

        $documentos = DocumentoVehiculo::whereNull('reemplazado_por')
            ->where('estado', '!=', 'REEMPLAZADO')
            ->where(function ($q) use ($hoy, $proximo) {
                $q->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                    ->orWhere('fecha_vencimiento', '<', $hoy);
            })->get();

        foreach ($documentos as $doc) {
            $alerta = $this->evaluarDocumentoVehiculo($doc);
            if ($alerta) {
                $creadas++;
            }
        }

        return ['revisados' => $documentos->count(), 'creadas' => $creadas];
    }

    /**
     * Procesar todos los documentos de conductores pendientes (batch).
     * Usado por el comando check:document-expirations.
     *
     * @return array ['revisados' => int, 'creadas' => int]
     */
    public function procesarDocumentosConductoresBatch(): array
    {
        $creadas = 0;

        $documentos = DocumentoConductor::with('conductor')
            ->whereNull('reemplazado_por')
            ->where('activo', 1)
            ->get();

        foreach ($documentos as $doc) {
            $creadas += $this->evaluarDocumentoConductor($doc);
        }

        return ['revisados' => $documentos->count(), 'creadas' => $creadas];
    }

    // =========================================================================
    // METODOS PRIVADOS
    // =========================================================================

    /**
     * Evaluar la vigencia de una fecha de vencimiento.
     *
     * @return array{requiere_alerta: bool, tipo: string|null, dias: int}
     */
    private function evaluarVigencia($fechaVencimiento): array
    {
        $hoy = Carbon::today();
        $fecha = Carbon::parse($fechaVencimiento);
        $dias = $hoy->diffInDays($fecha, false);

        if ($fecha->lt($hoy)) {
            return ['requiere_alerta' => true, 'tipo' => 'VENCIDO', 'dias' => $dias];
        }

        if ($fecha->lte($hoy->copy()->addDays(self::DIAS_ANTICIPACION))) {
            return ['requiere_alerta' => true, 'tipo' => 'PROXIMO_VENCER', 'dias' => $dias];
        }

        return ['requiere_alerta' => false, 'tipo' => null, 'dias' => $dias];
    }

    /**
     * Verificar si ya existe una alerta activa para un documento de vehiculo.
     */
    private function existeAlertaVehiculo(int $idDocVehiculo, string $tipo): bool
    {
        return Alerta::where('tipo_vencimiento', $tipo)
            ->whereNull('deleted_at')
            ->where('id_doc_vehiculo', $idDocVehiculo)
            ->where('solucionada', false)
            ->exists();
    }

    /**
     * Evaluar licencia de conduccion con fechas por categoria.
     */
    private function evaluarLicenciaPorCategoria(DocumentoConductor $documento): int
    {
        $creadas = 0;
        $categoriasAMonitorear = $documento->getCategoriasAMonitorear();
        $fechasPorCategoria = $documento->fechas_por_categoria;

        foreach ($categoriasAMonitorear as $categoria) {
            if (!isset($fechasPorCategoria[$categoria]['fecha_vencimiento'])) {
                continue;
            }

            $fechaVencimiento = Carbon::parse($fechasPorCategoria[$categoria]['fecha_vencimiento']);
            $evaluacion = $this->evaluarVigencia($fechaVencimiento);

            if (!$evaluacion['requiere_alerta']) {
                continue;
            }

            // Verificar duplicado por categoria
            $mensajeBusqueda = "Licencia categoría {$categoria}";
            $existe = Alerta::where('tipo_vencimiento', $evaluacion['tipo'])
                ->whereNull('deleted_at')
                ->where('id_doc_conductor', $documento->id_doc_conductor)
                ->where('mensaje', 'like', "%{$mensajeBusqueda}%")
                ->where('solucionada', false)
                ->exists();

            if ($existe) {
                continue;
            }

            $nombreConductor = $documento->conductor
                ? "{$documento->conductor->nombre} {$documento->conductor->apellido}"
                : 'Conductor desconocido';

            Alerta::create([
                'tipo_alerta'      => 'CONDUCTOR',
                'id_doc_vehiculo'  => null,
                'id_doc_conductor' => $documento->id_doc_conductor,
                'tipo_vencimiento' => $evaluacion['tipo'],
                'mensaje'          => sprintf(
                    'Licencia categoría %s (%s) - %s - vence: %s',
                    $categoria,
                    $documento->numero_documento,
                    $nombreConductor,
                    $fechaVencimiento->format('Y-m-d')
                ),
                'fecha_alerta'     => Carbon::today(),
                'leida'            => 0,
                'solucionada'      => false,
                'visible_para'     => 'TODOS',
                'creado_por'       => null,
            ]);
            $creadas++;
        }

        return $creadas;
    }

    /**
     * Evaluar documento de conductor simple (no licencia por categoria).
     */
    private function evaluarDocumentoConductorSimple(DocumentoConductor $documento): int
    {
        if (!$documento->fecha_vencimiento) {
            return 0;
        }

        $evaluacion = $this->evaluarVigencia($documento->fecha_vencimiento);

        if (!$evaluacion['requiere_alerta']) {
            return 0;
        }

        // Verificar duplicado
        $existe = Alerta::where('tipo_vencimiento', $evaluacion['tipo'])
            ->whereNull('deleted_at')
            ->where('id_doc_conductor', $documento->id_doc_conductor)
            ->where('solucionada', false)
            ->exists();

        if ($existe) {
            return 0;
        }

        $nombreConductor = $documento->conductor
            ? "{$documento->conductor->nombre} {$documento->conductor->apellido}"
            : 'Conductor desconocido';

        Alerta::create([
            'tipo_alerta'      => 'CONDUCTOR',
            'id_doc_vehiculo'  => null,
            'id_doc_conductor' => $documento->id_doc_conductor,
            'tipo_vencimiento' => $evaluacion['tipo'],
            'mensaje'          => sprintf(
                'Documento %s (%s) - %s - vence: %s',
                $documento->tipo_documento,
                $documento->numero_documento,
                $nombreConductor,
                Carbon::parse($documento->fecha_vencimiento)->format('Y-m-d')
            ),
            'fecha_alerta'     => Carbon::today(),
            'leida'            => 0,
            'solucionada'      => false,
            'visible_para'     => 'TODOS',
            'creado_por'       => null,
        ]);

        return 1;
    }
}
