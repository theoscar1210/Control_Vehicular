<?php

namespace Database\Factories;

use App\Models\Alertas;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Alertas> */
class AlertasFactory extends Factory
{
    protected $model = Alertas::class;

    public function definition(): array
    {
        return [
            'id_documento_vehiculo' => null,
            'id_documento_conductor' => null,
            'tipo_alerta' => $this->faker->randomElement(['Proximo a vencer', 'Vencido']),
            'fecha_generacion' => now(),
            'estado' => 'Pendiente',
        ];
    }

    public function atendida(): static
    {
        return $this->state(fn() => ['estado' => 'Atendida']);
    }
}
