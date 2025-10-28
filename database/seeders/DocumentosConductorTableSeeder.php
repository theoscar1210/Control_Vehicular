<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conductor;
use App\Models\DocumentoConductor;

class DocumentosConductorTableSeeder extends Seeder
{
    public function run()
    {
        $conductores = Conductor::all();

        if ($conductores->count() === 0) {
            $conductores = Conductor::factory(10)->create();
        }

        foreach ($conductores as $con) {
            // cada conductor 1-3 documentos
            $count = rand(1, 3);
            DocumentoConductor::factory($count)->create([
                'id_conductor' => $con->id_conductor,
                'creado_por' => null,
            ]);
        }
    }
}
