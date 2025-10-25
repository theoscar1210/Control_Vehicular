
<?php


namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Models\DocumentoVehiculo;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //para cada vehiculo crear 1 SOAT y 1 Tecnomecanica (algunos proximos a vencer)
        Vehiculo::all()->each(function (vehiculo $veh) {
            //soat algunos proximos a vencer

            DocumentoVehiculo::factory()->state(function () {
                return rand(1, 4) === 1 ? ['fecha_vencimiento' => Carbon::now()->addDays(rand(1, 25))] : [];
            })->create([
                'id_vehiculo' => $veh->id_vehiculo,
                'tipo_documento' => 'SOAT',

            ]);

            //Tecnomecanica 

            DocumentoVehiculo::factory()->state(function () {
                return rand(1, 6) === 1 ? ['fecha_vencimiento' => Carbon::now()->subDays(rand(1, 60))] : [];
            })->create([
                'id_vehiculo' => $veh->id_vehiculo,
                'tipo_documento' => 'Tecnomecanica',
            ]);
        });

        //para cada conductor crear 1 licencia de conduccion (algunos proximos a vencer)
        Conductor::all()->each(function (Conductor $conductor) {
            DocumentoConductor::factory()->state(function () {
                return rand(1, 5) === 1 ? ['fecha_vencimiento' => Carbon::now()->addDays(rand(1, 30))] : [];
            })->create([
                'id_conductor' => $conductor->id_conductor,
                'tipo_documento' => 'Licencia de Conducir',
            ]);
        });
    }
}
