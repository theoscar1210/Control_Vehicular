<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\DocumentoVehiculo;

class DocumentosVehiculoTableSeeder extends Seeder
{
    public function run()
    {
        $vehiculos = Vehiculo::all();

        if ($vehiculos->count() === 0) {
            $vehiculos = Vehiculo::factory(10)->create();
        }

        foreach ($vehiculos as $veh) {
            // cada vehiculo 1-4 documentos
            $count = rand(1, 4);
            DocumentoVehiculo::factory($count)->create([
                'id_vehiculo' => $veh->id_vehiculo,
                'creado_por' => null,
            ]);
        }
    }
}
