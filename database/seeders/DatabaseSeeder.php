<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            PropietariosTableSeeder::class,
            ConductoresTableSeeder::class,
            VehiculosTableSeeder::class,
            DocumentosVehiculoTableSeeder::class,
            DocumentosConductorTableSeeder::class,
            AlertasTableSeeder::class,
        ]);
    }
}
