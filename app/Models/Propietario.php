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
    protected $primaryKey = 'id_propietario';
    public $incrementing = true;

    public $timestamps = false;


    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'nombre',              // Nombre del propietario
        'apellido',            // Apellido del propietario
        'tipo_doc',      // Tipo de documento (ej. CC, TI, CE)
        'identificacion',      // Número de identificación
        'creado_por',          // Usuario que creó el registro
        'fecha_registro'       // Fecha de registro del propietario
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // relaciones
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_propietario', 'id_propietario');
    }

    /**
     * Relación: el propietario tiene un vehículo
     * - Usa la clase 'vehiculo'
     * - Clave foránea en la tabla 'vehiculos': 'id_propietario'
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
