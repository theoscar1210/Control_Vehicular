<?php

namespace Database\Seeders;

use App\Models\Propietario;
use App\models\Conductor;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;


class PropietarioConductorVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // crear 10 propietarios y 10 conductores
        $propietarios = Propietario::factory(10)->create();
        $conductores = Conductor::factory(10)->create();

        //crear vehiculos asignando propietarios y conductores aleatoriamente

        foreach ($propietarios as $i => $prop) {
            // crear entre 1 y 2 vehiculos por propietario
            $cantidad = rand(1, 2);
            for ($j = 0; $j < $cantidad; $j++) {
                $vehiculo = Vehiculo::factory()->create([
                    'id_propietario' => $prop->id_propietario,
                    'id_conductor' => $conductores->random()->id_conductor,

                ]);
            }

            // opcional: guardar id_vehiculo en propietario si lo deseas (deja nullable)
            // $prop->update(['id_vehiculo' => $vehiculo->id_vehiculo]);
        }
    }
}
