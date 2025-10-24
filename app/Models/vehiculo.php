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

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'marca',                      // Marca del vehículo (ej. Toyota)
        'modelo',                     // Modelo del vehículo (ej. Corolla)
        'color',                      // Color del vehículo
        'placa',                      // Placa del vehículo
        'numero_licencia_transito',   // Número de la licencia de tránsito
        'id_propietario',             // Clave foránea al propietario
        'id_conductor',                // Clave foránea al conductor
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
