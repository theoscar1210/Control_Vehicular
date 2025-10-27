<?php

// Define el espacio de nombres del modelo
namespace App\Models;

// Importa clases necesarias para el modelo
use Illuminate\Database\Eloquent\Model; // Clase base para modelos Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar factories para pruebas

// Define el modelo DocumentoVehiculo que extiende Eloquent
class DocumentoVehiculo extends Model
{
    // Habilita el uso de factories para generar datos de prueba
    use HasFactory;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'documentos_vehiculo';
    protected $primaryKey = 'id_doc_vehiculo';
    public $timestamps = false; // Desactiva las marcas de tiempo automáticas

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'id_vehiculo',
        'tipo_documento',
        'numero_documento',
        'entidad_emisora',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'activo',
        'creado_por',
        'fecha_registro',

    ];

    /** Conversión automática de tipos de datos */
    protected $casts = [
        'activo' => 'boolean', // Convierte el campo 'activo' a booleano
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación: el documento pertenece a un vehículo
     * - Usa la clase Vehiculo
     * - Laravel buscará en la tabla 'vehiculos' el campo 'id_vehiculo'
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /**
     * Relación: el documento puede tener muchas alertas asociadas
     * - Usa la clase Alerta
     * - Laravel buscará en la tabla 'alertas' el campo 'id_documento_vehiculo'
     */
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_documento_vehiculo');
    }
}
