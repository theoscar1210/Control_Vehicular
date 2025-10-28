<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';
    protected $primaryKey = 'id_alerta';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tipo_alerta',
        'id_doc_vehiculo',
        'id_doc_conductor',
        'tipo_vencimiento',
        'mensaje',
        'fecha_alerta',
        'leida',
        'visible_para',
        'creado_por',
        'fecha_registro',

    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_alerta' => 'date',
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación: la alerta pertenece a un documento del vehículo
     * - Usa la clase DocumentoVehiculo
     * - Laravel buscará en la tabla 'documentos_vehiculo' el campo 'id_documento_vehiculo'
     */
    public function documentoVehiculo()
    {
        return $this->belongsTo(DocumentoVehiculo::class, 'id_doc_vehiculo');
    }
    public function documentoConductor()
    {
        return $this->belongsTo(DocumentoConductor::class, 'id_doc_conductor');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
