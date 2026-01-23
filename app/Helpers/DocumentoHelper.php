<?php

namespace App\Helpers;

use Carbon\Carbon;

class DocumentoHelper
{
    /**
     * Determinar el estado del documento según la fecha de vencimiento.
     *
     * Devuelve: 'VENCIDO' | 'POR_VENCER' | 'VIGENTE'
     *
     * @param string|\DateTimeInterface $fechaVencimiento
     * @return string
     */
    /**
     * Determinar el estado del documento según la fecha de vencimiento.
     *
     * Devuelve: 'VENCIDO' | 'POR_VENCER' | 'VIGENTE'
     *
     * Reglas de días:
     * - VENCIDO: días < 0
     * - POR_VENCER: días 0-20
     * - VIGENTE: días > 20
     */
    public static function determinarEstado($fechaVencimiento): string
    {
        if (empty($fechaVencimiento)) {
            return 'VIGENTE';
        }

        $vencimiento = Carbon::parse($fechaVencimiento)->startOfDay();
        $hoy = Carbon::today();
        $diasParaVencer = $hoy->diffInDays($vencimiento, false);

        if ($diasParaVencer < 0) {
            return 'VENCIDO';
        } elseif ($diasParaVencer <= 20) {
            return 'POR_VENCER';
        } else {
            return 'VIGENTE';
        }
    }

    /**
     * Determinar la clase CSS según los días restantes.
     *
     * Reglas de colores:
     * - Rojo (danger): VENCIDO o 0-5 días
     * - Amarillo (warning): 6-20 días
     * - Verde (success): más de 20 días
     *
     * @param int $dias Días restantes (negativo si vencido)
     * @return string Clase CSS de Bootstrap
     */
    public static function determinarClase(int $dias): string
    {
        if ($dias < 0 || $dias <= 5) {
            return 'danger';
        } elseif ($dias <= 20) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}
