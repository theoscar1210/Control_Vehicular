<?php

namespace App\Services;

use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de negocio para la gestion del ciclo de vida de documentos vehiculares.
 *
 * Responsabilidades:
 * - Calcular estado y vencimiento segun tipo de documento
 * - Gestionar versionamiento (crear nueva version, marcar anterior como reemplazado)
 * - Coordinar con AlertaService para generar/solucionar alertas
 * - Encapsular reglas de negocio de Tecnomecanica, SOAT, etc.
 */
class DocumentoVehiculoService
{
    /** Tipos de documentos que requieren fecha de vencimiento */
    private const DOCUMENTOS_CON_VENCIMIENTO = [
        'SOAT',
        'TECNOMECANICA',
        'POLIZA',
    ];

    private AlertaService $alertaService;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Crear un nuevo documento para un vehiculo.
     * Gestiona versionamiento, estado y alertas automaticamente.
     *
     * @param Vehiculo $vehiculo El vehiculo al que pertenece el documento
     * @param array $datos Datos validados del documento
     * @return DocumentoVehiculo El documento creado
     */
    public function crearDocumento(Vehiculo $vehiculo, array $datos): DocumentoVehiculo
    {
        return DB::transaction(function () use ($vehiculo, $datos) {
            $tipo = $datos['tipo_documento'];

            // Actualizar fecha de matricula si es Tarjeta de Propiedad
            if ($tipo === 'TARJETA PROPIEDAD' && !empty($datos['fecha_matricula'])) {
                $vehiculo->update([
                    'fecha_matricula' => Carbon::parse($datos['fecha_matricula'])->startOfDay()
                ]);
                $vehiculo->refresh();
            }

            // Calcular fechas y estado
            $fechaEmision = !empty($datos['fecha_emision'])
                ? Carbon::parse($datos['fecha_emision'])->startOfDay()
                : null;

            $resultado = $this->calcularVencimientoYEstado($vehiculo, $tipo, $fechaEmision);

            // Obtener version anterior
            $ultimoDocumento = $this->obtenerUltimaVersion($vehiculo->id_vehiculo, $tipo);
            $version = $ultimoDocumento ? $ultimoDocumento->version + 1 : 1;

            // Crear documento (estado es accessor computado, no se persiste)
            $nuevoDocumento = DocumentoVehiculo::create([
                'id_vehiculo'       => $vehiculo->id_vehiculo,
                'tipo_documento'    => $tipo,
                'numero_documento'  => $datos['numero_documento'],
                'entidad_emisora'   => $datos['entidad_emisora'] ?? null,
                'fecha_emision'     => $fechaEmision,
                'fecha_vencimiento' => $resultado['fecha_vencimiento'],
                'activo'            => 1,
                'version'           => $version,
                'nota'              => $datos['nota'] ?? null,
                'creado_por'        => auth()->id() ?? null,
            ]);

            // Reemplazar version anterior
            if ($ultimoDocumento) {
                $this->reemplazarDocumento($ultimoDocumento, $nuevoDocumento);
            }

            // Evaluar y generar alerta si aplica
            $this->alertaService->evaluarDocumentoVehiculo($nuevoDocumento);

            return $nuevoDocumento;
        });
    }

    /**
     * Renovar un documento existente (crear nueva version).
     *
     * @param Vehiculo $vehiculo El vehiculo del documento
     * @param DocumentoVehiculo $documentoAnterior El documento a renovar
     * @param array $datos Datos validados de la renovacion
     * @return DocumentoVehiculo El nuevo documento
     *
     * @throws \InvalidArgumentException Si el documento no es renovable
     */
    public function renovarDocumento(Vehiculo $vehiculo, DocumentoVehiculo $documentoAnterior, array $datos): DocumentoVehiculo
    {
        if (!$this->esRenovable($documentoAnterior)) {
            throw new \InvalidArgumentException(
                'Solo se pueden renovar documentos con vencimiento que esten vencidos o proximos a vencer.'
            );
        }

        return DB::transaction(function () use ($vehiculo, $documentoAnterior, $datos) {
            $fechaEmision = Carbon::parse($datos['fecha_emision'])->startOfDay();
            $resultado = $this->calcularVencimientoYEstado(
                $vehiculo,
                $documentoAnterior->tipo_documento,
                $fechaEmision
            );

            // Crear nueva version
            $nuevoDocumento = DocumentoVehiculo::create([
                'id_vehiculo'       => $vehiculo->id_vehiculo,
                'tipo_documento'    => $documentoAnterior->tipo_documento,
                'numero_documento'  => $datos['numero_documento'],
                'entidad_emisora'   => $datos['entidad_emisora'] ?? null,
                'fecha_emision'     => $fechaEmision,
                'fecha_vencimiento' => $resultado['fecha_vencimiento'],
                'estado'            => $resultado['estado'],
                'activo'            => 1,
                'version'           => $documentoAnterior->version + 1,
                'nota'              => $datos['nota'] ?? null,
                'creado_por'        => auth()->id() ?? null,
            ]);

            // Reemplazar documento anterior
            $this->reemplazarDocumento($documentoAnterior, $nuevoDocumento);

            // Evaluar y generar alerta si aplica
            $this->alertaService->evaluarDocumentoVehiculo($nuevoDocumento);

            return $nuevoDocumento;
        });
    }

    /**
     * Verificar si un documento es renovable.
     */
    public function esRenovable(DocumentoVehiculo $documento): bool
    {
        return in_array($documento->tipo_documento, self::DOCUMENTOS_CON_VENCIMIENTO)
            && in_array($documento->estado, ['VENCIDO', 'POR_VENCER']);
    }

    /**
     * Verificar si un tipo de documento requiere vencimiento.
     */
    public function requiereVencimiento(string $tipo): bool
    {
        return in_array($tipo, self::DOCUMENTOS_CON_VENCIMIENTO);
    }

    // =========================================================================
    // METODOS PRIVADOS
    // =========================================================================

    /**
     * Calcular fecha de vencimiento y estado segun tipo de documento.
     *
     * Reglas:
     * - SOAT/Poliza: vencimiento a 1 ano desde emision
     * - Tecnomecanica: depende de fecha de matricula del vehiculo
     *   - Vehiculos nuevos (Carro): primera revision a 5 anos
     *   - Motos: primera revision a 2 anos
     *   - Despues de primera revision: renovacion anual
     * - Tarjeta Propiedad: sin vencimiento
     *
     * @return array{fecha_vencimiento: Carbon|null, estado: string}
     */
    private function calcularVencimientoYEstado(Vehiculo $vehiculo, string $tipo, ?Carbon $fechaEmision): array
    {
        $fechaVencimiento = null;
        $estado = 'VIGENTE';

        if (!in_array($tipo, self::DOCUMENTOS_CON_VENCIMIENTO) || !$fechaEmision) {
            return ['fecha_vencimiento' => $fechaVencimiento, 'estado' => $estado];
        }

        if ($tipo === 'TECNOMECANICA') {
            $fechaVencimiento = $vehiculo->calcularVencimientoTecnomecanica($fechaEmision);
        } else {
            $fechaVencimiento = $fechaEmision->copy()->addYear();
        }

        if ($fechaVencimiento) {
            $dias = Carbon::today()->diffInDays($fechaVencimiento, false);
            if ($dias < 0) {
                $estado = 'VENCIDO';
            } elseif ($dias <= 20) {
                $estado = 'POR_VENCER';
            }
        }

        return ['fecha_vencimiento' => $fechaVencimiento, 'estado' => $estado];
    }

    /**
     * Obtener la ultima version activa de un tipo de documento para un vehiculo.
     */
    private function obtenerUltimaVersion(int $idVehiculo, string $tipo): ?DocumentoVehiculo
    {
        return DocumentoVehiculo::where('id_vehiculo', $idVehiculo)
            ->where('tipo_documento', $tipo)
            ->where('estado', '!=', 'REEMPLAZADO')
            ->lockForUpdate()
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Marcar un documento como reemplazado y solucionar sus alertas.
     */
    private function reemplazarDocumento(DocumentoVehiculo $anterior, DocumentoVehiculo $nuevo): void
    {
        $anterior->update([
            'estado'          => 'REEMPLAZADO',
            'reemplazado_por' => $nuevo->id_doc_vehiculo,
            'activo'          => 0,
        ]);

        $this->alertaService->solucionarPorRenovacionVehiculo($anterior->id_doc_vehiculo);
    }
}
