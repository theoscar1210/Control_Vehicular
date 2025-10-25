<?php

namespace Database\Factories;

use App\Models\Propietario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Propietario>
 */
class PropietarioFactory extends Factory
{
    protected $model = Propietario::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'tipo_documento' => $this->faker->randomElement(['CC', 'CE', 'NIT']),
            'identificacion' => $this->faker->unique()->numerify('##########'),
            'id_vehiculo' => null,
        ];
    }
}
