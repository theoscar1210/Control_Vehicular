<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;

class AlertasTableSeeder extends Seeder
{
    public function run()
    {
        // Generar alertas automáticas desde documentos existentes
        // Alertas por documentos vehiculo vencidos o por vencer
        DocumentoVehiculo::where('estado', 'VENCIDO')->get()->each(function ($doc) {
            \App\Models\Alerta::factory()->create([
                'tipo_alerta' => 'VEHICULO',
                'id_doc_vehiculo' => $doc->id_doc_vehiculo,
                'id_doc_conductor' => null,
                'tipo_vencimiento' => 'VENCIDO',
                'mensaje' => "{$doc->tipo_documento} del vehículo {$doc->id_vehiculo} VENCIDO (vence: {$doc->fecha_vencimiento})",
                'fecha_alerta' => now()->format('Y-m-d'),
            ]);
        });

        DocumentoVehiculo::where('estado', 'POR_VENCER')->get()->each(function ($doc) {
            \App\Models\Alerta::factory()->create([
                'tipo_alerta' => 'VEHICULO',
                'id_doc_vehiculo' => $doc->id_doc_vehiculo,
                'id_doc_conductor' => null,
                'tipo_vencimiento' => 'PROXIMO_VENCER',
                'mensaje' => "{$doc->tipo_documento} del vehículo {$doc->id_vehiculo} PROXIMO A VENCER (vence: {$doc->fecha_vencimiento})",
                'fecha_alerta' => now()->format('Y-m-d'),
            ]);
        });

        DocumentoConductor::where('estado', 'VENCIDO')->get()->each(function ($doc) {
            \App\Models\Alerta::factory()->create([
                'tipo_alerta' => 'CONDUCTOR',
                'id_doc_vehiculo' => null,
                'id_doc_conductor' => $doc->id_doc_conductor,
                'tipo_vencimiento' => 'VENCIDO',
                'mensaje' => "{$doc->tipo_documento} del conductor {$doc->id_conductor} VENCIDO (vence: {$doc->fecha_vencimiento})",
                'fecha_alerta' => now()->format('Y-m-d'),
            ]);
        });

        DocumentoConductor::where('estado', 'POR_VENCER')->get()->each(function ($doc) {
            \App\Models\Alerta::factory()->create([
                'tipo_alerta' => 'CONDUCTOR',
                'id_doc_vehiculo' => null,
                'id_doc_conductor' => $doc->id_doc_conductor,
                'tipo_vencimiento' => 'PROXIMO_VENCER',
                'mensaje' => "{$doc->tipo_documento} del conductor {$doc->id_conductor} PROXIMO A VENCER (vence: {$doc->fecha_vencimiento})",
                'fecha_alerta' => now()->format('Y-m-d'),
            ]);
        });

        // Adicionalmente, agregar algunas alertas aleatorias para pruebas UI
        Alerta::factory(10)->create();
    }
}
