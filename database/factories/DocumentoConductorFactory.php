<?php

namespace Database\Factories;

use App\Models\DocumentoConductor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**@extends Factory<DocumentoConductor> */
class DocumentoConductorFactory extends Factory
{
    protected $model = DocumentoConductor::class;

    public function definition(): array
    {
        $exp = $this->faker->dateTimeBetween('-3 years', 'now');
        $venc = $this->faker->dateTimeBetween('-1 years', '+1 years');

        $estado = (Carbon::parse($venc)->isPast()) ? 'Vencido' : 'Vigente';
        return [
            'id_conductor' => null,
            'tipo_documento' => $this->faker->randomElement(['Cédula', 'Licencia de Conducir']),
            'numero_documento' => strtoupper($this->faker->bothify('DOC-#####')),
            'fecha_expedicion' => Carbon::parse($exp)->toDateString(),
            'fecha_vencimiento' => Carbon::parse($venc)->toDateString(),
            'estado' => $estado,
        ];
    }

    public function vencido(): static
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => Carbon::now()->subDays(5)->toDateString(),
            'estado' => 'Vencido',
        ]);
    }
}
