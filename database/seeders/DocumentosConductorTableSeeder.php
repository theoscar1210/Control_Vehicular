<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\Usuario;
use Carbon\Carbon;

class DocumentosConductorTableSeeder extends Seeder
{
    public function run()
    {
        $conductores = Conductor::all();
        $creator = Usuario::first();

        if ($conductores->isEmpty()) {
            $this->command->warn('Ejecute primero ConductoresTableSeeder');
            return;
        }

        $tiposDocumento = ['Licencia Conducción', 'EPS', 'ARL', 'Certificado Médico'];
        $categoriasLicencia = ['A1', 'A2', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3'];
        $entidades = [
            'Licencia Conducción' => ['Secretaría de Movilidad'],
            'EPS' => ['Sura EPS', 'Nueva EPS', 'Sanitas', 'Compensar', 'Famisanar'],
            'ARL' => ['Sura ARL', 'Positiva ARL', 'Colmena ARL', 'AXA Colpatria ARL'],
            'Certificado Médico' => ['IPS Salud Total', 'Centro Médico Colsanitas', 'Clínica del Country'],
        ];

        foreach ($conductores as $conductor) {
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

                // Licencias tienen vencimiento más largo
                if ($tipo === 'Licencia Conducción' && $estado === 'VIGENTE') {
                    $fechaVencimiento = Carbon::now()->addYears(rand(2, 8));
                }

                $fechaEmision = $fechaVencimiento->copy()->subYear();
                $entidad = $entidades[$tipo][array_rand($entidades[$tipo])];

                // Generar número de documento realista
                $numDoc = match($tipo) {
                    'Licencia Conducción' => 'LIC-' . $conductor->identificacion,
                    'EPS' => 'EPS-' . rand(100000, 999999),
                    'ARL' => 'ARL-' . rand(100000, 999999),
                    'Certificado Médico' => 'CM-' . date('Y') . '-' . rand(1000, 9999),
                    default => 'DOC-' . rand(100000, 999999),
                };

                $data = [
                    'id_conductor' => $conductor->id_conductor,
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
                ];

                // Agregar categoría solo para licencias
                if ($tipo === 'Licencia Conducción') {
                    $data['categoria_licencia'] = $categoriasLicencia[array_rand($categoriasLicencia)];
                }

                DocumentoConductor::create($data);
            }
        }
    }
}
