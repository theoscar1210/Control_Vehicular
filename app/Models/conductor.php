<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Conductor extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'conductores';
    protected $primaryKey = 'id_conductor';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_doc',
        'identificacion',
        'telefono',
        'telefono_emergencia',
        'activo',
        'creado_por',
        'fecha_registro',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_registro' => 'datetime',
    ];

    /**
     * Configuración de auditoría de cambios
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'apellido', 'tipo_doc', 'identificacion', 'telefono', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Conductor {$this->nombre} {$this->apellido} {$eventName}");
    }

    /**
     * Hook de Eloquent: al eliminar un conductor
     * - Si es soft delete, se eliminan (soft) documentos y vehículos asociados
     * - Si es force delete, se eliminan físicamente
     */
    protected static function booted()
    {
        static::deleting(function ($conductor) {
            if (!$conductor->isForceDeleting()) {
                // Soft delete documentos
                $conductor->documentosConductor()->delete();

                // Soft delete vehículos y sus documentos
                $conductor->vehiculos()->each(function ($veh) {
                    $veh->documentos()->delete();
                    $veh->delete();
                });
            } else {
                // Force delete documentos y vehículos
                $conductor->documentosConductor()->withTrashed()->forceDelete();
                $conductor->vehiculos()->withTrashed()->forceDelete();
            }
        });
    }

    /**
     * Relación: el conductor tiene muchos vehículos (legacy - mantener por compatibilidad)
     * @deprecated Usar vehiculosAsignados() para relación muchos a muchos
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_conductor', 'id_conductor');
    }

    /**
     * Relación: vehículos asignados al conductor (muchos a muchos)
     */
    public function vehiculosAsignados()
    {
        return $this->belongsToMany(Vehiculo::class, 'conductor_vehiculo', 'id_conductor', 'id_vehiculo')
            ->withPivot('es_principal', 'fecha_asignacion', 'fecha_desasignacion', 'activo')
            ->wherePivot('activo', true);
    }

    /**
     * Relación: todos los vehículos (incluyendo inactivos) para historial
     */
    public function todosLosVehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'conductor_vehiculo', 'id_conductor', 'id_vehiculo')
            ->withPivot('es_principal', 'fecha_asignacion', 'fecha_desasignacion', 'activo');
    }

    /**
     * Relación: el conductor tiene muchos documentos
     */
    public function documentosConductor()
    {
        return $this->hasMany(DocumentoConductor::class, 'id_conductor', 'id_conductor');
    }

    /**
     * Relación: el conductor fue creado por un usuario
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
