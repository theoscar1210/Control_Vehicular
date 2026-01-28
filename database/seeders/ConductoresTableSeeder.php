<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conductor;
use App\Models\Usuario;

class ConductoresTableSeeder extends Seeder
{
    public function run()
    {
        $creator = Usuario::first();

        $conductores = [
            [
                'nombre' => 'José Luis',
                'apellido' => 'Ramírez Torres',
                'tipo_doc' => 'CC',
                'identificacion' => '1015234567',
                'telefono' => '3101234567',
                'telefono_emergencia' => '3209876543',
                'activo' => true,
            ],
            [
                'nombre' => 'Andrés Felipe',
                'apellido' => 'Moreno Díaz',
                'tipo_doc' => 'CC',
                'identificacion' => '80567890',
                'telefono' => '3154567890',
                'telefono_emergencia' => '3187654321',
                'activo' => true,
            ],
            [
                'nombre' => 'Luis Carlos',
                'apellido' => 'Pérez Jiménez',
                'tipo_doc' => 'CC',
                'identificacion' => '79234561',
                'telefono' => '3201112233',
                'telefono_emergencia' => '3114455667',
                'activo' => true,
            ],
            [
                'nombre' => 'Miguel Ángel',
                'apellido' => 'López Castillo',
                'tipo_doc' => 'CC',
                'identificacion' => '1098234567',
                'telefono' => '3167778899',
                'telefono_emergencia' => '3051234567',
                'activo' => true,
            ],
            [
                'nombre' => 'Diego Fernando',
                'apellido' => 'Vargas Muñoz',
                'tipo_doc' => 'CC',
                'identificacion' => '10789012',
                'telefono' => '3124567891',
                'telefono_emergencia' => '3189012345',
                'activo' => true,
            ],
            [
                'nombre' => 'Jhon Alexander',
                'apellido' => 'Castro Medina',
                'tipo_doc' => 'CC',
                'identificacion' => '1052345678',
                'telefono' => '3001122334',
                'telefono_emergencia' => '3115566778',
                'activo' => true,
            ],
            [
                'nombre' => 'Eduardo',
                'apellido' => 'González Rojas',
                'tipo_doc' => 'CC',
                'identificacion' => '80345678',
                'telefono' => '3176543210',
                'telefono_emergencia' => '3014567890',
                'activo' => true,
            ],
            [
                'nombre' => 'William',
                'apellido' => 'Suárez Ríos',
                'tipo_doc' => 'CC',
                'identificacion' => '79876543',
                'telefono' => '3139876543',
                'telefono_emergencia' => '3026789012',
                'activo' => false, // Conductor inactivo
            ],
            [
                'nombre' => 'Ricardo',
                'apellido' => 'Mendoza Silva',
                'tipo_doc' => 'CE',
                'identificacion' => 'E123456',
                'telefono' => '3148765432',
                'telefono_emergencia' => '3033456789',
                'activo' => true,
            ],
            [
                'nombre' => 'Oscar Javier',
                'apellido' => 'Ruiz Parra',
                'tipo_doc' => 'CC',
                'identificacion' => '1087654321',
                'telefono' => '3192345678',
                'telefono_emergencia' => '3046789012',
                'activo' => true,
            ],
        ];

        foreach ($conductores as $data) {
            Conductor::create(array_merge($data, [
                'creado_por' => $creator?->id_usuario,
                'fecha_registro' => now(),
            ]));
        }
    }
}
