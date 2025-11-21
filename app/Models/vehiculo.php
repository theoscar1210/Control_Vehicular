<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'vehiculos';

    /** Clave primaria personalizada */
    protected $primaryKey = 'id_vehiculo';
    public $timestamps = false;
    protected $keyType = 'int';
    public $incrementing = true;

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'color',
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
     * Hook de Eloquent: al eliminar un vehículo
     * - Si es soft delete, se eliminan (soft) documentos y propietario asociado
     * - Si es force delete, se eliminan físicamente
     */
    protected static function booted()
    {
        static::deleting(function ($veh) {
            if (!$veh->isForceDeleting()) {
                // Soft delete documentos del vehículo
                $veh->documentos()->delete();

                //  En vez de borrar propietario, desasignar
                if ($veh->propietario) {
                    $veh->id_propietario = null;
                    $veh->save();
                }
            } else {
                // Force delete documentos y propietario
                $veh->documentos()->withTrashed()->forceDelete();
            }
        });
    }

    /**
     * Relación: el vehículo pertenece a un propietario
     */
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario', 'id_propietario');
    }

    /**
     * Relación: el vehículo pertenece a un conductor
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor', 'id_conductor');
    }

    /**
     * Relación: el vehículo fue creado por un usuario
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /**
     * Relación: documentos del vehículo
     */
    public function documentos()
    {
        return $this->hasMany(DocumentoVehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    /**
     * Relación alternativa: documentos del vehículo
     */
    public function documentosVehiculo()
    {
        return $this->hasMany(DocumentoVehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }
}
