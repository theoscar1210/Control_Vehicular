<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propietario;
use App\Models\Usuario;

class PropietariosTableSeeder extends Seeder
{
    public function run()
    {
        $creator = Usuario::first();

        $propietarios = [
            [
                'nombre' => 'Transportes del Valle',
                'apellido' => 'S.A.S',
                'tipo_doc' => 'NIT',
                'identificacion' => '900123456',
            ],
            [
                'nombre' => 'Carlos Alberto',
                'apellido' => 'Rodríguez Gómez',
                'tipo_doc' => 'CC',
                'identificacion' => '1020345678',
            ],
            [
                'nombre' => 'María Fernanda',
                'apellido' => 'García López',
                'tipo_doc' => 'CC',
                'identificacion' => '52987654',
            ],
            [
                'nombre' => 'LogiCarga',
                'apellido' => 'Ltda',
                'tipo_doc' => 'NIT',
                'identificacion' => '800456789',
            ],
            [
                'nombre' => 'Juan Pablo',
                'apellido' => 'Martínez Ruiz',
                'tipo_doc' => 'CC',
                'identificacion' => '80123987',
            ],
            [
                'nombre' => 'Distribuciones Andinas',
                'apellido' => 'S.A',
                'tipo_doc' => 'NIT',
                'identificacion' => '860789123',
            ],
            [
                'nombre' => 'Sandra Milena',
                'apellido' => 'Hernández Castro',
                'tipo_doc' => 'CC',
                'identificacion' => '1098765432',
            ],
            [
                'nombre' => 'Pedro Antonio',
                'apellido' => 'Sánchez Vargas',
                'tipo_doc' => 'CC',
                'identificacion' => '79456123',
            ],
        ];

        foreach ($propietarios as $data) {
            Propietario::firstOrCreate(
                ['identificacion' => $data['identificacion']],
                array_merge($data, [
                    'creado_por' => $creator?->id_usuario,
                    'fecha_registro' => now(),
                ])
            );
        }
    }
}
