<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Conductor;
use App\Models\Propietario;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition()
    {
        $tipos = ['Carro', 'Moto', 'Camion', 'Otro'];
        return [
            'placa' => strtoupper($this->faker->bothify('???-####')),
            'marca' => $this->faker->company(),
            'modelo' => $this->faker->optional()->word(),
            'color' => $this->faker->safeColorName(),
            'tipo' => $this->faker->randomElement($tipos),

            //por defecto creara propietario si no se pasa uno
            'id_propietario' => Propietario::factory(),
            'id_conductor' => null,
            'estado' => 'Activo',
            'clasificacion' => 'EMPLEADO',
            'creado_por' => null,
            'fecha_registro' => now(),
        ];
    }

    public function withConductor($conductorId = null)
    {
        if ($conductorId) {
            return $this->state(fn() => ['id_conductor' => $conductorId]);
        }
        return $this->state(fn() => ['id_conductor' => Conductor::factory()]);
    }

    public function inactive()
    {
        return $this->state(fn() => ['estado' => 'Inactivo']);
    }

    public function contratista()
    {
        return $this->state(fn() => ['clasificacion' => 'CONTRATISTA']);
    }

    public function familiar()
    {
        return $this->state(fn() => ['clasificacion' => 'FAMILIAR']);
    }
}
