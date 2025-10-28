<?php

namespace Database\Factories;

use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Alertas> */
class AlertaFactory extends Factory
{
    protected $model = Alerta::class;

    public function definition()
    {
        $tipo_alerta = $this->faker->randomElement(['VEHICULO', 'CONDUCTOR']);
        $id_doc_vehiculo = null;
        $id_doc_conductor = null;
        $mensaje = null;
        $fecha_alerta = $this->faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d');
        $tipo_vencimiento = $this->faker->randomElement(['VENCIDO', 'PROXIMO_VENCER']);

        if ($tipo_alerta === 'VEHICULO') {
            $doc = DocumentoVehiculo::factory()->state(fn() => [
                'fecha_vencimiento' => $tipo_vencimiento === 'VENCIDO'
                    ? $this->faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d')
                    : $this->faker->dateTimeBetween('now', '+20 days')->format('Y-m-d'),
                'estado' => $tipo_vencimiento === 'VENCIDO' ? 'VENCIDO' : 'POR_VENCER'
            ])->create();

            $id_doc_vehiculo = $doc->id_doc_vehiculo;
            $mensaje = "{$doc->tipo_documento} del vehículo {$doc->id_vehiculo} está {$doc->estado} (vence: {$doc->fecha_vencimiento})";
        } else {
            $doc = DocumentoConductor::factory()->state(fn() => [
                'fecha_vencimiento' => $tipo_vencimiento === 'VENCIDO'
                    ? $this->faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d')
                    : $this->faker->dateTimeBetween('now', '+20 days')->format('Y-m-d'),
                'estado' => $tipo_vencimiento === 'VENCIDO' ? 'VENCIDO' : 'POR_VENCER'
            ])->create();

            $id_doc_conductor = $doc->id_doc_conductor;
            $mensaje = "{$doc->tipo_documento} del conductor {$doc->id_conductor} está {$doc->estado} (vence: {$doc->fecha_vencimiento})";
        }

        return [
            'tipo_alerta' => $tipo_alerta,
            'id_doc_vehiculo' => $id_doc_vehiculo,
            'id_doc_conductor' => $id_doc_conductor,
            'tipo_vencimiento' => $tipo_vencimiento,
            'mensaje' => $mensaje,
            'fecha_alerta' => $fecha_alerta,
            'leida' => 0,
            'visible_para' => $this->faker->randomElement(['ADMIN', 'SST', 'PORTERIA', 'TODOS']),
            'creado_por' => null,
            'fecha_registro' => now(),
        ];
    }
}
