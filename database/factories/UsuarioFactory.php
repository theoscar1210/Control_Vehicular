<?php

namespace Database\Factories;


use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        $roles = ['ADMIN', 'SST', 'PORTERIA'];

        return [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'usuario' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // por defecto para pruebas
            'rol' => $this->faker->randomElement($roles),
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin()
    {
        return $this->state(fn() => ['rol' => 'ADMIN']);
    }

    public function porter()
    {
        return $this->state(fn() => ['rol' => 'PORTERIA']);
    }
}
