<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';
    protected $primaryKey = 'id_alerta';

    protected $fillable = [
        'id_documento_vehiculo',
        'id_documento_conductor', // Clave foránea al documento del conductor
        'tipo_alerta',           // Tipo de alerta (vencimiento, renovación, etc.)
        'fecha_alerta',          // Fecha en que se generó la alerta
        'fecha_generacion',      // Fecha en que se envió la alerta
        'estado',                // Estado de la alerta (pendiente, enviada, resuelta)
    ];

    protected $casts = [
        'fecha_alerta' => 'datetime',
    ];

    /**
     * Relación: la alerta pertenece a un documento del vehículo
     * - Usa la clase DocumentoVehiculo
     * - Laravel buscará en la tabla 'documentos_vehiculo' el campo 'id_documento_vehiculo'
     */
    public function documentoVehiculo()
    {
        return $this->belongsTo(DocumentoVehiculo::class, 'id_documento_vehiculo');
    }
    public function documentoConductor()
    {
        return $this->belongsTo(DocumentoConductor::class, 'id_documento_conductor');
    }
}
