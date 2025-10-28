<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Usuario;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Usuarios fijos útiles para pruebas
        Usuario::factory()->admin()->create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'usuario' => 'admin',
            'email' => 'admin@example.com',
        ]);

        Usuario::factory()->create([
            'nombre' => 'Porteria',
            'apellido' => 'Prueba',
            'usuario' => 'porteria',
            'email' => 'porteria@example.com',
            'rol' => 'PORTERIA',
        ]);

        // algunos usuarios aleatorios
        Usuario::factory(3)->create();
    }
}
