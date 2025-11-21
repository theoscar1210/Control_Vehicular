<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoConductor extends Model
{
    use HasFactory, SoftDeletes;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'documentos_conductor';
    protected $primaryKey = 'id_doc_conductor';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    /** Constantes para tipos y estados */
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
        'ruta_archivo',
        'creado_por',
        'version',
        'reemplazado_por',
        'nota',
        'fecha_registro'
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
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor', 'id_conductor');
    }

    /**
     * Relación: el documento fue creado por un usuario
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /**
     * Relación: el documento puede tener muchas alertas asociadas
     */
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_doc_conductor');
    }

    /**
     * Relación: documento que lo reemplaza
     */
    public function reemplazo()
    {
        return $this->belongsTo(self::class, 'reemplazado_por', 'id_doc_conductor');
    }

    /**
     * Relación: versiones que derivan de este documento
     */
    public function versiones()
    {
        return $this->hasMany(self::class, 'reemplazado_por', 'id_doc_conductor');
    }

    /**
     * Mutador: normaliza el estado antes de guardar
     */
    public function setEstadoAttribute($value)
    {
        $value = strtoupper(str_replace(' ', '_', (string) $value));
        if (!in_array($value, self::ESTADOS)) {
            $value = 'VIGENTE';
        }
        $this->attributes['estado'] = $value;
    }
}
