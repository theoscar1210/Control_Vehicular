<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            // Model cast 'password' => 'hashed' se encargará de hashear la contraseña
            'password' => 'password',
            'rol' => $this->faker->randomElement(['SST', 'Seguridad', 'Administrador']),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function administrador(): static
    {
        return $this->state(fn() => ['rol' => 'Administrador']);
    }

    public function sst(): static
    {
        return $this->state(fn() => ['rol' => 'SST']);
    }
    public function seguridad(): static
    {
        return $this->state(fn() => ['rol' => 'Seguridad']);
    }
}
