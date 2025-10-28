<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conductor;
use App\Models\User;
use App\Models\Usuario;

class ConductoresTableSeeder extends Seeder
{
    public function run()
    {
        $creator = Usuario::first() ?? Usuario::factory()->create();

        Conductor::factory(10)->create(['creado_por' => $creator->id_usuario]);
    }
}
