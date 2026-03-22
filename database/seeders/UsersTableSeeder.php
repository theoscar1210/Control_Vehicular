<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Administrador principal
        Usuario::firstOrCreate(
            ['usuario' => 'admin'],
            [
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'email' => 'admin@controlvehicular.com',
                'password' => Hash::make('password'),
                'rol' => 'ADMIN',
                'activo' => true,
            ]
        );

    }
}
