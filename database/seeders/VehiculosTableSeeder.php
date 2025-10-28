<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\Conductor;

class VehiculosTableSeeder extends Seeder
{
    public function run()
    {
        // crear vehiculos asociados a propietarios existentes
        $propietarios = Propietario::all();
        $conductores = Conductor::all();

        // si no existen crear algunos con factory
        if ($propietarios->count() === 0) {
            $propietarios = Propietario::factory(5)->create();
        }

        if ($conductores->count() === 0) {
            $conductores = Conductor::factory(5)->create();
        }

        // crear 20 vehiculos, algunos sin conductor, algunos con
        for ($i = 0; $i < 20; $i++) {
            $prop = $propietarios->random();
            $veh = Vehiculo::factory()->create([
                'id_propietario' => $prop->id_propietario,
                'creado_por' => null,
            ]);

            // asignar conductor aleatoriamente 50% de probabilidad
            if (rand(0, 1) === 1) {
                $veh->id_conductor = $conductores->random()->id_conductor;
                $veh->save();
            }
        }
    }
}
