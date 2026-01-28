<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\DocumentoVehiculo;
use App\Models\Usuario;
use Carbon\Carbon;

class DocumentosVehiculoTableSeeder extends Seeder
{
    public function run()
    {
        $vehiculos = Vehiculo::all();
        $creator = Usuario::first();

        if ($vehiculos->isEmpty()) {
            $this->command->warn('Ejecute primero VehiculosTableSeeder');
            return;
        }

        $tiposDocumento = ['SOAT', 'Tecnomecanica', 'Tarjeta Propiedad'];
        $entidades = [
            'SOAT' => ['Seguros Bolivar', 'Sura', 'Liberty Seguros', 'Mapfre', 'Allianz'],
            'Tecnomecanica' => ['CDA Centro Diagnóstico', 'CDA El Dorado', 'Revicar S.A.S', 'Diagnosticentro'],
            'Tarjeta Propiedad' => ['Ministerio de Transporte'],
        ];

        foreach ($vehiculos as $vehiculo) {
            foreach ($tiposDocumento as $tipo) {
                // Determinar estado aleatorio pero realista
                $random = rand(1, 10);

                if ($random <= 6) {
                    // 60% VIGENTE
                    $fechaVencimiento = Carbon::now()->addMonths(rand(3, 11));
                    $estado = 'VIGENTE';
                } elseif ($random <= 8) {
                    // 20% POR_VENCER (próximos 30 días)
                    $fechaVencimiento = Carbon::now()->addDays(rand(5, 25));
                    $estado = 'POR_VENCER';
                } else {
                    // 20% VENCIDO
                    $fechaVencimiento = Carbon::now()->subDays(rand(5, 60));
                    $estado = 'VENCIDO';
                }

                $fechaEmision = $fechaVencimiento->copy()->subYear();
                $entidad = $entidades[$tipo][array_rand($entidades[$tipo])];

                // Generar número de documento realista
                $numDoc = match($tipo) {
                    'SOAT' => 'POL-' . rand(1000000, 9999999),
                    'Tecnomecanica' => 'RTM-' . $vehiculo->placa . '-' . rand(1000, 9999),
                    'Tarjeta Propiedad' => 'TP-' . str_replace('-', '', $vehiculo->placa),
                    default => 'DOC-' . rand(100000, 999999),
                };

                DocumentoVehiculo::create([
                    'id_vehiculo' => $vehiculo->id_vehiculo,
                    'tipo_documento' => $tipo,
                    'numero_documento' => $numDoc,
                    'entidad_emisora' => $entidad,
                    'fecha_emision' => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado' => $estado,
                    'activo' => true,
                    'version' => 1,
                    'creado_por' => $creator?->id_usuario,
                    'fecha_registro' => now(),
                ]);
            }
        }
    }
}
