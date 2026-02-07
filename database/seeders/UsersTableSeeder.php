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

        // Usuario SST
        Usuario::firstOrCreate(
            ['usuario' => 'sst'],
            [
                'nombre' => 'Carolina',
                'apellido' => 'Mejía Torres',
                'email' => 'sst@controlvehicular.com',
                'password' => Hash::make('password'),
                'rol' => 'SST',
                'activo' => true,
            ]
        );

        // Usuario Portería 1
        Usuario::firstOrCreate(
            ['usuario' => 'porteria'],
            [
                'nombre' => 'Jorge',
                'apellido' => 'Castañeda',
                'email' => 'porteria@controlvehicular.com',
                'password' => Hash::make('password'),
                'rol' => 'PORTERIA',
                'activo' => true,
            ]
        );

        // Usuario Portería 2
        Usuario::firstOrCreate(
            ['usuario' => 'porteria2'],
            [
                'nombre' => 'Martha',
                'apellido' => 'Velásquez',
                'email' => 'porteria2@controlvehicular.com',
                'password' => Hash::make('password'),
                'rol' => 'PORTERIA',
                'activo' => true,
            ]
        );

        // Otro administrador
        Usuario::firstOrCreate(
            ['usuario' => 'rgomez'],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Gómez Pérez',
                'email' => 'rgomez@controlvehicular.com',
                'password' => Hash::make('password'),
                'rol' => 'ADMIN',
                'activo' => true,
            ]
        );
    }
}
