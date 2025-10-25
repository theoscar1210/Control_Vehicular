<?php

namespace Database\Seeders;

use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use App\Models\Alertas;
use Dom\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AlertasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::now();
        $thresholdDays = 30; // documentos con vencimiento en <= 30 días seran Proximos a vencer

        // documentos de vehiculos
        DocumentoVehiculo::all()->each(function (DocumentoVehiculo $doc) use ($today, $thresholdDays) {
            if (!$doc->fecha_vencimiento) {
                return;
            }

            $venc = Carbon::parse($doc->fecha_vencimiento);

            if ($venc->isPast()) {
                Alertas::factory()->create([
                    'id_documento_vehiculo' => $doc->id,
                    'id_documento_conductor' => null,
                    'tipo_alerta' => 'Vencido',
                    'fecha_generacion' => now(),
                    'estado' => 'Pendiente',
                ]);
            } elseif ($venc->diffInDays($today, false) <= $thresholdDays) {
                Alertas::factory()->create([
                    'id_documento_vehiculo' => $doc->id,
                    'id_documento_conductor' => null,
                    'tipo_alerta' => 'Próximo a vencer',
                    'fecha_generacion' => now(),
                    'estado' => 'Pendiente',
                ]);
            }
        });
        // documentos de conductores
        DocumentoConductor::all()->each(function (DocumentoConductor $doc) use ($today, $thresholdDays) {
            if (!$doc->fecha_vencimiento) {
                return;
            }

            $venc = Carbon::parse($doc->fecha_vencimiento);

            if ($venc->isPast()) {
                Alertas::factory()->create([
                    'id_documento_vehiculo' => null,
                    'id_documento_conductor' => $doc->id,
                    'tipo_alerta' => 'Vencido',
                    'fecha_generacion' => now(),
                    'estado' => 'Pendiente',
                ]);
            } elseif ($venc->diffInDays($today, false) <= $thresholdDays) {
                Alertas::factory()->create([
                    'id_documento_vehiculo' => null,
                    'id_documento_conductor' => $doc->id,
                    'tipo_alerta' => 'Próximo a vencer',
                    'fecha_generacion' => now(),
                    'estado' => 'Pendiente',
                ]);
            }
        });
    }
}
