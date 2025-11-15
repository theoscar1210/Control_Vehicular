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
    protected $primaryKey = 'id_doc_conductor';
    public $timestamps = false; // Desactiva las marcas de tiempo automáticas
    public $incrementing = true;
    protected $keyType = 'int';
    public const TIPOS = ['Licencia Conducción', 'EPS', 'ARL', 'Certificado Médico', 'Otro'];
    public const ESTADOS = ['VIGENTE', 'POR_VENCER', 'VENCIDO', 'REEMPLAZADO'];
    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'id_conductor',
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
        'activo' => 'boolean',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_registro' => 'datetime',
    ];


    /**
     * Relación: el documento pertenece a un conductor
     * - Usa la clase Conductor
     * - Laravel buscará en la tabla 'conductores' el campo 'id_conductor'
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor', 'id_conductor');
    }
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /**
     * Relación: el documento puede tener muchas alertas asociadas
     * - Usa la clase Alerta
     * - Laravel buscará en la tabla 'alertas' el campo 'id_documento_conductor'
     */
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_doc_conductor');
    }

    public function setEstadoAttribute($value)
    {
        $value = strtoupper(str_replace(' ', '_', (string) $value));
        if (!in_array($value, self::ESTADOS)) {
            $value = 'VIGENTE';
        }
        $this->attributes['estado'] = $value;
    }
}
