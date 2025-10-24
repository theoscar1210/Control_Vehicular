<?php

// Define el espacio de nombres del modelo
namespace App\Models;

// Importa clases necesarias para el modelo
use Illuminate\Database\Eloquent\Model; // Clase base para modelos Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar factories para pruebas

// Define el modelo Propietario que extiende Eloquent
class Propietario extends Model
{
    // Habilita el uso de factories para generar datos de prueba
    use HasFactory;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'propietarios';

    /** Clave primaria personalizada (por defecto sería 'id') */
    protected $primaryKey = 'id_empleado';

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'nombre',              // Nombre del propietario
        'apellido',            // Apellido del propietario
        'tipo_documento',      // Tipo de documento (ej. CC, TI, CE)
        'identificacion',      // Número de identificación
        'id_vehiculo',         // Clave foránea al vehículo (relación uno a uno)
    ];

    /**
     * Relación: el propietario tiene un vehículo
     * - Usa la clase 'vehiculo'
     * - Clave foránea en la tabla 'vehiculos': 'id_propietario'
     */
    public function vehiculo()
    {
        return $this->hasOne(Vehiculo::class, 'id_propietario');
    }
}
