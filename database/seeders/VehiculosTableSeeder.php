<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\Conductor;
use Carbon\Carbon;

class VehiculosTableSeeder extends Seeder
{
    public function run()
    {
        $propietarios = Propietario::all();
        $conductores = Conductor::where('activo', true)->get();

        if ($propietarios->isEmpty() || $conductores->isEmpty()) {
            $this->command->warn('Ejecute primero PropietariosTableSeeder y ConductoresTableSeeder');
            return;
        }

        $vehiculos = [
            // Carros
            [
                'placa' => 'BXG-245',
                'marca' => 'Chevrolet',
                'modelo' => 'Spark GT 2022',
                'color' => 'Blanco',
                'tipo' => 'Carro',
                'fecha_matricula' => '2022-03-15',
            ],
            [
                'placa' => 'CJK-891',
                'marca' => 'Renault',
                'modelo' => 'Logan 2021',
                'color' => 'Gris',
                'tipo' => 'Carro',
                'fecha_matricula' => '2021-06-20',
            ],
            [
                'placa' => 'DPQ-456',
                'marca' => 'Kia',
                'modelo' => 'Picanto 2023',
                'color' => 'Rojo',
                'tipo' => 'Carro',
                'fecha_matricula' => '2023-01-10',
            ],
            [
                'placa' => 'ERS-789',
                'marca' => 'Mazda',
                'modelo' => 'CX-30 2022',
                'color' => 'Negro',
                'tipo' => 'Carro',
                'fecha_matricula' => '2022-08-05',
            ],
            [
                'placa' => 'FTU-123',
                'marca' => 'Toyota',
                'modelo' => 'Corolla 2020',
                'color' => 'Plata',
                'tipo' => 'Carro',
                'fecha_matricula' => '2020-04-18',
            ],
            [
                'placa' => 'GVW-567',
                'marca' => 'Hyundai',
                'modelo' => 'Accent 2021',
                'color' => 'Azul',
                'tipo' => 'Carro',
                'fecha_matricula' => '2021-11-22',
            ],
            [
                'placa' => 'HXY-890',
                'marca' => 'Nissan',
                'modelo' => 'Versa 2022',
                'color' => 'Blanco',
                'tipo' => 'Carro',
                'fecha_matricula' => '2022-02-14',
            ],
            [
                'placa' => 'JZA-234',
                'marca' => 'Volkswagen',
                'modelo' => 'Gol 2019',
                'color' => 'Negro',
                'tipo' => 'Carro',
                'fecha_matricula' => '2019-09-30',
            ],
            // Camiones
            [
                'placa' => 'KBC-678',
                'marca' => 'Chevrolet',
                'modelo' => 'NHR 2020',
                'color' => 'Blanco',
                'tipo' => 'Camion',
                'fecha_matricula' => '2020-07-12',
            ],
            [
                'placa' => 'LCD-901',
                'marca' => 'Hino',
                'modelo' => '300 Series 2021',
                'color' => 'Blanco',
                'tipo' => 'Camion',
                'fecha_matricula' => '2021-03-08',
            ],
            [
                'placa' => 'MDE-345',
                'marca' => 'JAC',
                'modelo' => 'X200 2022',
                'color' => 'Rojo',
                'tipo' => 'Camion',
                'fecha_matricula' => '2022-05-25',
            ],
            [
                'placa' => 'NEF-789',
                'marca' => 'Foton',
                'modelo' => 'Aumark 2021',
                'color' => 'Azul',
                'tipo' => 'Camion',
                'fecha_matricula' => '2021-10-15',
            ],
            // Motos
            [
                'placa' => 'OGH-12A',
                'marca' => 'Honda',
                'modelo' => 'CB 125F 2023',
                'color' => 'Rojo',
                'tipo' => 'Moto',
                'fecha_matricula' => '2023-02-28',
            ],
            [
                'placa' => 'PIJ-34B',
                'marca' => 'Yamaha',
                'modelo' => 'FZ 150 2022',
                'color' => 'Negro',
                'tipo' => 'Moto',
                'fecha_matricula' => '2022-09-10',
            ],
            [
                'placa' => 'QKL-56C',
                'marca' => 'Suzuki',
                'modelo' => 'Gixxer 250 2022',
                'color' => 'Azul',
                'tipo' => 'Moto',
                'fecha_matricula' => '2022-12-05',
            ],
            [
                'placa' => 'RMN-78D',
                'marca' => 'AKT',
                'modelo' => 'NKD 125 2021',
                'color' => 'Negro',
                'tipo' => 'Moto',
                'fecha_matricula' => '2021-08-18',
            ],
            // Vehículo inactivo
            [
                'placa' => 'SOP-90E',
                'marca' => 'Renault',
                'modelo' => 'Sandero 2018',
                'color' => 'Gris',
                'tipo' => 'Carro',
                'fecha_matricula' => '2018-05-10',
                'estado' => 'Inactivo',
            ],
        ];

        $propIdx = 0;
        $condIdx = 0;

        foreach ($vehiculos as $data) {
            // Asignar propietario cíclicamente
            $propietario = $propietarios[$propIdx % $propietarios->count()];
            $propIdx++;

            // Asignar conductor a algunos vehículos (80%)
            $conductor = null;
            if (rand(1, 10) <= 8 && $conductores->isNotEmpty()) {
                $conductor = $conductores[$condIdx % $conductores->count()];
                $condIdx++;
            }

            Vehiculo::create(array_merge($data, [
                'id_propietario' => $propietario->id_propietario,
                'id_conductor' => $conductor?->id_conductor,
                'estado' => $data['estado'] ?? 'Activo',
                'fecha_matricula' => Carbon::parse($data['fecha_matricula']),
                'creado_por' => null,
                'fecha_registro' => now(),
            ]));
        }
    }
}
