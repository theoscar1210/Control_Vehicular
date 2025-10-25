<?php

namespace Database\Factories;

use App\Models\Conductor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Conductor> */
class ConductorFactory extends Factory
{
    protected $model = Conductor::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'licencia' => $this->faker->unique()->bothify('LIC-####-??'),
            'identificacion' => $this->faker->unique()->numerify('###########'),
        ];
    }
}
