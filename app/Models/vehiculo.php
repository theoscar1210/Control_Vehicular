<?php

// Define el espacio de nombres del modelo
namespace App\Models;

// Importa clases necesarias para el modelo
use Illuminate\Database\Eloquent\Model; // Clase base para modelos Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar factories para pruebas

// Define el modelo 'vehiculo' que extiende Eloquent
class Vehiculo extends Model
{
    // Habilita el uso de factories para generar datos de prueba
    use HasFactory;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'vehiculos';

    /** Clave primaria personalizada (por defecto sería 'id') */
    protected $primaryKey = 'id_vehiculo';
    public $timestamps = false;

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'tipo',
        'id_propietario',
        'id_conductor',
        'estado',
        'creado_por',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];
    /**
     * Relación: el vehículo pertenece a un propietario
     * - Usa la clase Propietario
     * - Clave foránea: 'id_propietario'
     */
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    /**
     * Relación: el vehículo pertenece a un conductor
     * - Usa la clase Conductor
     * - Clave foránea: 'id_conductor'
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /**
     * Relación: el vehículo tiene muchos documentos
     * - Usa la clase DocumentoVehiculo
     * - Clave foránea en la tabla relacionada: 'id_vehiculo'
     */
    public function documentos()
    {
        return $this->hasMany(DocumentoVehiculo::class, 'id_vehiculo');
    }
}
