<?php

namespace App\Services;

use Carbon\Carbon;

class DocumentStatusService
{
    /**
     * Calcula el estado general de documentos de un vehículo.
     * Retorna: SIN_DOCUMENTOS | VENCIDO | POR_VENCER | VIGENTE
     */
    public function calcularEstadoGeneral($vehiculo): array
    {
        $documentos = $vehiculo->documentos;

        if ($documentos->isEmpty()) {
            return [
                'estado' => 'SIN_DOCUMENTOS',
                'clase' => 'secondary',
                'icono' => 'fas fa-question-circle',
                'texto' => 'Sin documentos'
            ];
        }

        $tieneVencido = $documentos->where('estado', 'VENCIDO')->count() > 0;
        $tienePorVencer = $documentos->where('estado', 'POR_VENCER')->count() > 0;

        if ($tieneVencido) {
            return [
                'estado' => 'VENCIDO',
                'clase' => 'danger',
                'icono' => 'fas fa-times-circle',
                'texto' => 'Documentos vencidos'
            ];
        }

        if ($tienePorVencer) {
            return [
                'estado' => 'POR_VENCER',
                'clase' => 'warning',
                'icono' => 'fas fa-exclamation-triangle',
                'texto' => 'Próximo a vencer'
            ];
        }

        return [
            'estado' => 'VIGENTE',
            'clase' => 'success',
            'icono' => 'fas fa-check-circle',
            'texto' => 'Documentos vigentes'
        ];
    }

    /**
     * Calcula el estado detallado de cada tipo de documento de un vehículo.
     * Incluye documentos del conductor asignado.
     */
    public function calcularEstadosDetallados($vehiculo): array
    {
        $hoy = Carbon::today();
        $estados = [];

        $tiposVehiculo = ['SOAT', 'TECNOMECANICA', 'TARJETA PROPIEDAD', 'POLIZA'];
        foreach ($tiposVehiculo as $tipo) {
            $doc = $vehiculo->documentos
                ->where('tipo_documento', $tipo)
                ->where('activo', 1)
                ->first();

            if (!$doc) {
                $estados["vehiculo_{$tipo}"] = [
                    'estado' => 'SIN_REGISTRO',
                    'clase' => 'secondary',
                    'documento' => null,
                    'dias_restantes' => null,
                    'mensaje' => 'No registrado',
                    'fecha' => null
                ];
            } else {
                $diasRestantes = $doc->diasRestantes();
                $dias = $hoy->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                $estados["vehiculo_{$tipo}"] = [
                    'estado' => $doc->estado,
                    'clase' => $this->getClaseEstado($doc->estado, $dias),
                    'documento' => $doc,
                    'dias_restantes' => $diasRestantes,
                    'mensaje' => $this->getMensajeEstado($doc->estado, $dias),
                    'fecha' => $doc->fecha_vencimiento ? Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : null,
                    'dias' => $dias
                ];
            }
        }

        if ($vehiculo->conductor) {
            $tiposConductor = ['LICENCIA CONDUCCION'];
            foreach ($tiposConductor as $tipo) {
                $doc = $vehiculo->conductor->documentosConductor
                    ->where('tipo_documento', $tipo)
                    ->where('activo', 1)
                    ->first();

                if (!$doc) {
                    $estados["conductor_{$tipo}"] = [
                        'estado' => 'SIN_REGISTRO',
                        'clase' => 'secondary',
                        'documento' => null,
                        'dias_restantes' => null,
                        'mensaje' => 'No registrado',
                        'fecha' => null,
                        'dias' => null
                    ];
                } else {
                    $dias = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento), false);
                    $estados["conductor_{$tipo}"] = [
                        'estado' => $doc->estado,
                        'clase' => $this->getClaseEstado($doc->estado, $dias),
                        'documento' => $doc,
                        'dias_restantes' => $dias,
                        'mensaje' => $this->getMensajeEstado($doc->estado, $dias),
                        'fecha' => $doc->fecha_vencimiento ? Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : null,
                        'dias' => $dias
                    ];
                }
            }
        }

        return $estados;
    }

    public function getClaseEstado(string $estado, ?int $dias = null): string
    {
        if ($dias !== null) {
            if ($dias < 0 || $dias <= 5) {
                return 'danger';
            } elseif ($dias <= 20) {
                return 'warning';
            }
            return 'success';
        }

        return match ($estado) {
            'VIGENTE' => 'success',
            'POR_VENCER' => 'warning',
            'VENCIDO' => 'danger',
            default => 'secondary'
        };
    }

    public function getMensajeEstado(string $estado, ?int $dias = null): string
    {
        if ($dias === null) {
            return 'Estado desconocido';
        }

        return match ($estado) {
            'VIGENTE' => "Vigente ({$dias} días restantes)",
            'POR_VENCER' => "Vence en {$dias} días",
            'VENCIDO' => "Vencido hace " . abs($dias) . " días",
            default => 'Estado desconocido'
        };
    }
}
