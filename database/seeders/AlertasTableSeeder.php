<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Carbon\Carbon;

class AlertasTableSeeder extends Seeder
{
    public function run()
    {
        // Alertas por documentos de vehículo vencidos
        DocumentoVehiculo::with('vehiculo')
            ->where('estado', 'VENCIDO')
            ->get()
            ->each(function ($doc) {
                $placa = $doc->vehiculo?->placa ?? 'N/A';
                $fechaVenc = Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y');

                Alerta::firstOrCreate(
                    [
                        'id_doc_vehiculo' => $doc->id_doc_vehiculo,
                        'tipo_vencimiento' => 'VENCIDO',
                    ],
                    [
                        'tipo_alerta' => 'VEHICULO',
                        'id_doc_conductor' => null,
                        'mensaje' => "{$doc->tipo_documento} VENCIDO - Placa: {$placa} (venció: {$fechaVenc})",
                        'fecha_alerta' => now()->toDateString(),
                        'leida' => false,
                        'solucionada' => false,
                        'visible_para' => 'TODOS',
                    ]
                );
            });

        // Alertas por documentos de vehículo próximos a vencer
        DocumentoVehiculo::with('vehiculo')
            ->where('estado', 'POR_VENCER')
            ->get()
            ->each(function ($doc) {
                $placa = $doc->vehiculo?->placa ?? 'N/A';
                $fechaVenc = Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y');
                $diasRestantes = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento));

                Alerta::firstOrCreate(
                    [
                        'id_doc_vehiculo' => $doc->id_doc_vehiculo,
                        'tipo_vencimiento' => 'PROXIMO_VENCER',
                    ],
                    [
                        'tipo_alerta' => 'VEHICULO',
                        'id_doc_conductor' => null,
                        'mensaje' => "{$doc->tipo_documento} próximo a vencer - Placa: {$placa} (vence: {$fechaVenc}, {$diasRestantes} días)",
                        'fecha_alerta' => now()->toDateString(),
                        'leida' => false,
                        'solucionada' => false,
                        'visible_para' => 'TODOS',
                    ]
                );
            });

        // Alertas por documentos de conductor vencidos
        DocumentoConductor::with('conductor')
            ->where('estado', 'VENCIDO')
            ->get()
            ->each(function ($doc) {
                $conductor = $doc->conductor;
                $nombreConductor = $conductor ? "{$conductor->nombre} {$conductor->apellido}" : 'N/A';
                $fechaVenc = Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y');

                Alerta::firstOrCreate(
                    [
                        'id_doc_conductor' => $doc->id_doc_conductor,
                        'tipo_vencimiento' => 'VENCIDO',
                    ],
                    [
                        'tipo_alerta' => 'CONDUCTOR',
                        'id_doc_vehiculo' => null,
                        'mensaje' => "{$doc->tipo_documento} VENCIDO - Conductor: {$nombreConductor} (venció: {$fechaVenc})",
                        'fecha_alerta' => now()->toDateString(),
                        'leida' => false,
                        'solucionada' => false,
                        'visible_para' => 'TODOS',
                    ]
                );
            });

        // Alertas por documentos de conductor próximos a vencer
        DocumentoConductor::with('conductor')
            ->where('estado', 'POR_VENCER')
            ->get()
            ->each(function ($doc) {
                $conductor = $doc->conductor;
                $nombreConductor = $conductor ? "{$conductor->nombre} {$conductor->apellido}" : 'N/A';
                $fechaVenc = Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y');
                $diasRestantes = Carbon::now()->diffInDays(Carbon::parse($doc->fecha_vencimiento));

                Alerta::firstOrCreate(
                    [
                        'id_doc_conductor' => $doc->id_doc_conductor,
                        'tipo_vencimiento' => 'PROXIMO_VENCER',
                    ],
                    [
                        'tipo_alerta' => 'CONDUCTOR',
                        'id_doc_vehiculo' => null,
                        'mensaje' => "{$doc->tipo_documento} próximo a vencer - Conductor: {$nombreConductor} (vence: {$fechaVenc}, {$diasRestantes} días)",
                        'fecha_alerta' => now()->toDateString(),
                        'leida' => false,
                        'solucionada' => false,
                        'visible_para' => 'TODOS',
                    ]
                );
            });
    }
}
