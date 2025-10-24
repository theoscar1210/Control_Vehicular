<?php

// Define el espacio de nombres del modelo
namespace App\Models;

// Importa clases necesarias para el modelo
use Illuminate\Database\Eloquent\Model; // Clase base para modelos Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory; // Permite usar factories para pruebas

// Define el modelo DocumentoConductor que extiende Eloquent
class DocumentoConductor extends Model
{
    // Habilita el uso de factories para generar datos de prueba
    use HasFactory;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'documentos_conductor';

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'id_conductor',         // Clave foránea que relaciona el documento con un conductor
        'tipo_documento',       // Tipo de documento (ej. Licencia, Certificado médico)
        'numero_documento',     // Número del documento
        'fecha_expedicion',     // Fecha en que se expidió el documento
        'fecha_vencimiento',    // Fecha en que vence el documento
        'estado',               // Estado actual del documento (vigente, vencido, anulado)
    ];

    /** Conversión automática de tipos de datos */
    protected $casts = [
        'fecha_expedicion' => 'date',     // Convierte a objeto DateTime
        'fecha_vencimiento' => 'date',    // Convierte a objeto DateTime
    ];

    /**
     * Relación: el documento pertenece a un conductor
     * - Usa la clase Conductor
     * - Laravel buscará en la tabla 'conductores' el campo 'id_conductor'
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor');
    }

    /**
     * Relación: el documento puede tener muchas alertas asociadas
     * - Usa la clase Alerta
     * - Laravel buscará en la tabla 'alertas' el campo 'id_documento_conductor'
     */
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_documento_conductor');
    }
}
