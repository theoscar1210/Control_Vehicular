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
        } elseif ($diasParaVencer <= 30) {
            return 'POR_VENCER';
        } else {
            return 'VIGENTE';
        }
    }
}
