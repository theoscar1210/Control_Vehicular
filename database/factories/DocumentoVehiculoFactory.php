<?php

namespace Database\Factories;

use App\Models\DocumentoVehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<DocumentoVehiculo> */
class DocumentoVehiculoFactory extends Factory
{
    protected $model = DocumentoVehiculo::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['SOAT', 'TECNOMECANICA']);
        // Generar fecha_expedicion entre -2 años y hoy
        $exp = $this->faker->dateTimeBetween('-2 years', 'now');
        // fecha_vencimiento entre hoy -1y y +1y
        $venc = $this->faker->dateTimeBetween('-1 years', '+1 years');

        $estado = (Carbon::parse($venc)->isPast()) ? 'vencido' : 'vigente';

        return [
            'id_vehiculo' => null, // set in seeder
            'tipo_documento' => $tipo,
            'numero_documento' => strtoupper($this->faker->bothify('DOC-######')),
            'fecha_expedicion' => Carbon::parse($exp)->toDateString(),
            'fecha_vencimiento' => Carbon::parse($venc)->toDateString(),
            'estado' => $estado,
        ];
    }

    public function vigente(): static
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => Carbon::now()->addMonths(6)->toDateString(),
            'estado' => 'vigente',
        ]);
    }

    public function proximoAVencer(int $days = 20): static
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => Carbon::now()->addDays($days)->toDateString(),
            'estado' => 'vigente',
        ]);
    }

    public function vencido(): static
    {
        return $this->state(fn() => [
            'fecha_vencimiento' => Carbon::now()->subDays(10)->toDateString(),
            'estado' => 'vencido',
        ]);
    }
}
