<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propietario;
use App\Models\User;
use App\Models\Usuario;

class PropietariosTableSeeder extends Seeder
{
    public function run()
    {
        // algunos propietarios con creador
        $creator = Usuario::first() ?? Usuario::factory()->create();

        Propietario::factory(10)->create(['creado_por' => $creator->id_usuario]);
    }
}
