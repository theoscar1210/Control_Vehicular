<?php

namespace Database\Factories;

use App\Models\Conductor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Conductor> */
class ConductorFactory extends Factory
{
    protected $model = Conductor::class;

    public function definition()
    {
        $tipos = ['CC', 'CE'];
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'tipo_doc' => $this->faker->randomElement($tipos),
            'identificacion' => $this->faker->unique()->numerify('###########'),
            'telefono' => $this->faker->optional()->phoneNumber,
            'telefono_emergencia' => $this->faker->optional()->phoneNumber,
            'activo' => 1,
            'clasificacion' => 'EMPLEADO',
            'creado_por' => null,
            'fecha_registro' => now(),
        ];
    }

    public function contratista()
    {
        return $this->state(fn() => ['clasificacion' => 'CONTRATISTA']);
    }

    public function externo()
    {
        return $this->state(fn() => ['clasificacion' => 'EXTERNO']);
    }
}
