<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->administrador()->create([

            'nombre' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret123', //sera hasheada por el cast del modelo
        ]);

        //SST 
        User::factory()->sst()->create([
            'nombre' => 'SST User',
            'email' => 'sst@example.com',
            'password' => 'secret123',
        ]);

        // Seguridad
        User::factory()->seguridad()->create([
            'nombre' => 'Seguridad User',
            'email' => 'seguridad@example.com',
            'password' => 'secret123',
        ]);

        //usuarios aleatorios
        User::factory(5)->create();
    }
}
