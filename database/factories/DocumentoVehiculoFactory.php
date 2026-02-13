<?php

namespace Database\Factories;

use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentoVehiculo> */
class DocumentoVehiculoFactory extends Factory
{
    protected $model = DocumentoVehiculo::class;

    public function definition(): array
    {
        // Valores alineados con el ENUM definido en la DB
        $tipos = ['SOAT', 'TECNOMECANICA', 'TARJETA PROPIEDAD', 'POLIZA', 'OTRO'];
        $tipo = $this->faker->randomElement($tipos);

        // Generar fecha_emision entre hace 3 años y hoy; vencimiento entre -30 días y +365 días
        $fecha_emision = $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d');
        $fecha_vencimiento = $this->faker->dateTimeBetween('-30 days', '+365 days')->format('Y-m-d');

        // Determinar estado exclusivamente con los tokens válidos del ENUM
        $fv = Carbon::createFromFormat('Y-m-d', $fecha_vencimiento);
        $hoy = Carbon::now();

        if ($fv->lt($hoy)) {
            $estado = 'VENCIDO';
        } elseif ($fv->lte($hoy->copy()->addDays(30))) {
            $estado = 'POR_VENCER';
        } else {
            $estado = 'VIGENTE';
        }

        return [
            'id_vehiculo' => Vehiculo::factory(),
            'tipo_documento' => $tipo,
            'numero_documento' => strtoupper($this->faker->bothify('DOC-######')),
            'entidad_emisora' => $this->faker->company(),
            'fecha_emision' => $fecha_emision,
            'fecha_vencimiento' => $fecha_vencimiento,
            'estado' => $estado,
            'activo' => 1,
            'creado_por' => null,
            'fecha_registro' => now(),
        ];
    }

    public function expired(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => $this->faker->dateTimeBetween('-2 years', '-1 day')->format('Y-m-d'),
            'estado' => 'VENCIDO',
        ]);
    }

    public function nearExpiry(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => $this->faker->dateTimeBetween('now', '+20 days')->format('Y-m-d'),
            'estado' => 'POR_VENCER',
        ]);
    }
}
