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

    public function definition()
    {
        $tipos = ['CC', 'NIT'];
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'tipo_doc' => $this->faker->randomElement($tipos),
            'identificacion' => $this->faker->unique()->numerify('##########'),
            'creado_por' => null,
            'fecha_registro' => now(),
        ];
    }
}
