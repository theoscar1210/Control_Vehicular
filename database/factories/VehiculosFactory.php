<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculosFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        return [
            'marca' => $this->faker->randomElement(['Toyota', 'Ford', 'Chevrolet', 'Honda', 'Nissan']),
            'modelo' => $this->faker->bothify('Model-####'),
            'color' => $this->faker->safeColorName(),
            'placa' => strtoupper($this->faker->bothify('???-####')),
            'numero_licencia_transito' => $this->faker->optional()->bothify('LT-#######'),
            // id_propietario y id_conductor se asignan en el seeder
        ];
    }
}
