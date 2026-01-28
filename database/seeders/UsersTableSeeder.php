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
        Usuario::create([
            'nombre' => 'Administrador',
            'apellido' => 'Sistema',
            'usuario' => 'admin',
            'email' => 'admin@controlvehicular.com',
            'password' => Hash::make('password'),
            'rol' => 'ADMIN',
            'activo' => true,
        ]);

        // Usuario SST
        Usuario::create([
            'nombre' => 'Carolina',
            'apellido' => 'Mejía Torres',
            'usuario' => 'sst',
            'email' => 'sst@controlvehicular.com',
            'password' => Hash::make('password'),
            'rol' => 'SST',
            'activo' => true,
        ]);

        // Usuario Portería 1
        Usuario::create([
            'nombre' => 'Jorge',
            'apellido' => 'Castañeda',
            'usuario' => 'porteria',
            'email' => 'porteria@controlvehicular.com',
            'password' => Hash::make('password'),
            'rol' => 'PORTERIA',
            'activo' => true,
        ]);

        // Usuario Portería 2
        Usuario::create([
            'nombre' => 'Martha',
            'apellido' => 'Velásquez',
            'usuario' => 'porteria2',
            'email' => 'porteria2@controlvehicular.com',
            'password' => Hash::make('password'),
            'rol' => 'PORTERIA',
            'activo' => true,
        ]);

        // Otro administrador
        Usuario::create([
            'nombre' => 'Roberto',
            'apellido' => 'Gómez Pérez',
            'usuario' => 'rgomez',
            'email' => 'rgomez@controlvehicular.com',
            'password' => Hash::make('password'),
            'rol' => 'ADMIN',
            'activo' => true,
        ]);
    }
}
