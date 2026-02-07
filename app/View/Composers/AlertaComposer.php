<?php

namespace App\View\Composers;

use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertaComposer
{
    public function compose(View $view): void
    {
        $currentUser = Auth::user();

        if (!$currentUser || $currentUser->rol === 'ADMIN') {
            $view->with('alertasMenu', collect());
            $view->with('totalAlertasNoLeidas', 0);
            return;
        }

        $alertasMenu = Alerta::with([
                'documentoVehiculo.vehiculo.conductor',
                'documentoConductor.conductor',
                'usuariosQueLeyeron'
            ])
            ->whereNull('deleted_at')
            ->activas()
            ->conDocumentoVigente()
            ->where(function ($q) use ($currentUser) {
                $q->where('visible_para', 'TODOS')->orWhere('visible_para', $currentUser->rol);
            })
            ->noLeidasPor($currentUser->id_usuario)
            ->orderByDesc('fecha_alerta')
            ->take(5)
            ->get();

        $totalAlertasNoLeidas = Alerta::whereNull('deleted_at')
            ->activas()
            ->conDocumentoVigente()
            ->where(function ($q) use ($currentUser) {
                $q->where('visible_para', 'TODOS')->orWhere('visible_para', $currentUser->rol);
            })
            ->noLeidasPor($currentUser->id_usuario)
            ->count();

        $view->with('alertasMenu', $alertasMenu);
        $view->with('totalAlertasNoLeidas', $totalAlertasNoLeidas);
    }
}
