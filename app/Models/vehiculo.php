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
        'fecha_matricula',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_matricula' => 'date',
        'deleted_at' => 'datetime',
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
    /*************  Primera Tecnomecánica *************/
    /**
     * Calcula la fecha de primera tecnomecánica del vehículo según normativa:
     * - Carros: Primera revisión a los 5 años desde la fecha de matrícula
     * - Motos/Motocarros: Primera revisión a los 2 años desde la fecha de matrícula
     *
     * @return Carbon|null Fecha de primera tecnomecánica, o null si no tiene fecha de matrícula
     */
    public function fechaPrimeraTecnomecanica(): ?Carbon
    {
        if (!$this->fecha_matricula) {
            return null;
        }

        $fechaMatricula = Carbon::parse($this->fecha_matricula)->startOfDay();

        return match ($this->tipo) {
            'Carro' => $fechaMatricula->copy()->addYears(5),
            'Moto' => $fechaMatricula->copy()->addYears(2),
            default => $fechaMatricula->copy()->addYears(5), // Por defecto 5 años
        };
    }

    /**
     * Calcula la fecha de vencimiento de la Tecnomecánica considerando:
     * - Si es vehículo nuevo: fecha de primera revisión según tipo
     * - Si ya requiere revisión: fecha de emisión + 1 año (renovación anual)
     *
     * @param Carbon|null $fechaEmision Fecha de emisión del certificado (para renovaciones)
     * @return Carbon|null Fecha de vencimiento calculada
     */
    public function calcularVencimientoTecnomecanica(?Carbon $fechaEmision = null): ?Carbon
    {
        // Si no tiene fecha de matrícula, usar cálculo estándar (+1 año)
        if (!$this->fecha_matricula) {
            return $fechaEmision ? $fechaEmision->copy()->addYear() : null;
        }

        $fechaPrimeraRevision = $this->fechaPrimeraTecnomecanica();
        $hoy = Carbon::today();

        // Si la fecha de primera revisión aún no ha llegado
        if ($fechaPrimeraRevision && $hoy->lt($fechaPrimeraRevision)) {
            // El vencimiento es la fecha de primera revisión obligatoria
            return $fechaPrimeraRevision;
        }

        // Si ya pasó la fecha de primera revisión, el vencimiento es anual
        return $fechaEmision ? $fechaEmision->copy()->addYear() : null;
    }

    /**
     * Verifica si el vehículo ya requiere tener Tecnomecánica vigente
     *
     * @return bool True si ya pasó la fecha de primera revisión obligatoria
     */
    public function requiereTecnomecanica(): bool
    {
        $fecha = $this->fechaPrimeraTecnomecanica();

        return $fecha ? Carbon::today()->gte($fecha) : true;
    }

    /**
     * Obtiene los años para primera revisión según tipo de vehículo
     *
     * @return int Años hasta primera revisión (5 para carros, 2 para motos)
     */
    public function getAnosPrimeraRevisionAttribute(): int
    {
        return match ($this->tipo) {
            'Moto' => 2,
            default => 5,
        };
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
