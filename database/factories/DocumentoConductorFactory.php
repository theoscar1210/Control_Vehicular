<?php

namespace Database\Factories;

use App\Models\DocumentoConductor;
use App\Models\Conductor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentoConductor> */
class DocumentoConductorFactory extends Factory
{
    protected $model = DocumentoConductor::class;

    public function definition(): array
    {
        // Valores alineados con el ENUM definido en la DB para documentos de conductor
        $tipos = [
            'LICENCIA CONDUCCION',
            'EPS',
            'ARL',
            'CERTIFICADO MEDICO',
            'OTRO'
        ];
        $tipo = $this->faker->randomElement($tipos);

        // Generar fecha_emision entre hace 10 años y hoy; vencimiento entre -365 días y +3 años
        $fecha_emision = $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d');
        $fecha_vencimiento = $this->faker->dateTimeBetween('-365 days', '+3 years')->format('Y-m-d');

        // Determinar estado con tokens válidos del ENUM
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
            'id_conductor' => Conductor::factory(),
            'tipo_documento' => $tipo,
            'numero_documento' => strtoupper($this->faker->bothify('DOCC-######')),
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
            'fecha_vencimiento' => $this->faker->dateTimeBetween('-3 years', '-1 day')->format('Y-m-d'),
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
