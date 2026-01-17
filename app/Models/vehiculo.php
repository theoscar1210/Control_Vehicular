<?php

namespace App\Models;

use Carbon\Carbon;
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
        'deleted_at',

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
                    $veh->forceFill(['id_propietario' => null])->saveQuietly();
                }
            } else {
                // Force delete documentos y propietario
                $veh->documentos()->withTrashed()->forceDelete();
            }
        });
    }
    /*************  primera tecnomecánica *************/
    /**
     * Calcula la fecha de primera tecnomecánica del vehículo
     * - Carros: 5 años desde la fecha de matrícula
     * - Motos: 2 años desde la fecha de matrícula
     *
     * @return Carbon|null Fecha de primera tecnomecánica del vehículo, o null si no tiene fecha de matrícula
     */
    /*******    *******/
    public function fechaPrimeraTecnomecanica(): ?Carbon
    {
        if (!$this->fecha_matricula) {
            return null;
        }

        $base = $this->fecha_matricula->copy()->startOffDay();

        return match ($this->tipo) {
            'Carro' => $this->fecha_matricula->copy()->addYears(5),
            'Moto' => $this->fecha_matricula->copy()->addYears(2),
            default => null,
        };
    }

    /**
     * Comprueba si la fecha actual es mayor o igual que la fecha de primera tecnomecánica del vehículo
     *
     * @return bool Verdadero si la fecha actual es mayor o igual que la fecha de primera tecnomecánica, o false en caso contrario
     */
    public function requiereTecnomecanica(): bool
    {
        $fecha = $this->fechaPrimeraTecnomecanica();

        return $fecha ? now()->startOfDay()->greaterThanOrEqualTo($fecha) : false;
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



    /**
     * Accessor para obtener estado de SOAT
     */
    public function getEstadoSoatAttribute()
    {
        $soat = $this->documentosVehiculo()
            ->where('tipo_documento', 'SOAT')
            ->where('activo', 1)
            ->first();

        if (!$soat) {
            return [
                'estado' => 'SIN_REGISTRO',
                'clase' => 'secondary',
                'dias' => null,
                'fecha' => null
            ];
        }

        $hoy = \Carbon\Carbon::today();
        $vencimiento = \Carbon\Carbon::parse($soat->fecha_vencimiento);
        $dias = $hoy->diffInDays($vencimiento, false);

        if ($dias < 0) {
            $estado = 'VENCIDO';
            $clase = 'danger';
        } elseif ($dias <= 30) {
            $estado = 'POR_VENCER';
            $clase = 'warning';
        } else {
            $estado = 'VIGENTE';
            $clase = 'success';
        }

        return [
            'estado' => $estado,
            'clase' => $clase,
            'dias' => abs($dias),
            'fecha' => $vencimiento
        ];
    }

    /**
     * Accessor para obtener estado de Tecnomecánica
     */
    public function getEstadoTecnoAttribute()
    {
        $tecno = $this->documentosVehiculo()
            ->where('tipo_documento', 'Tecnomecanica')
            ->where('activo', 1)
            ->first();

        if (!$tecno) {
            return [
                'estado' => 'SIN_REGISTRO',
                'clase' => 'secondary',
                'dias' => null,
                'fecha' => null
            ];
        }

        $hoy = \Carbon\Carbon::today();
        $vencimiento = \Carbon\Carbon::parse($tecno->fecha_vencimiento);
        $dias = $hoy->diffInDays($vencimiento, false);

        if ($dias < 0) {
            $estado = 'VENCIDO';
            $clase = 'danger';
        } elseif ($dias <= 30) {
            $estado = 'POR_VENCER';
            $clase = 'warning';
        } else {
            $estado = 'VIGENTE';
            $clase = 'success';
        }

        return [
            'estado' => $estado,
            'clase' => $clase,
            'dias' => abs($dias),
            'fecha' => $vencimiento
        ];
    }

    /**
     * Scope para programar eliminación automática
     */
    public function scopeProgramarEliminacion($query)
    {
        return $query->onlyTrashed()
            ->where('deleted_at', '<=', now()->subMonths(6));
    }
}
