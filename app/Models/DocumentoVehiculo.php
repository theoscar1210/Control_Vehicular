<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Modelo para gestionar documentos de vehículos (SOAT, Tecnomecánica, etc.)
 */
class DocumentoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'documentos_vehiculo';
    protected $primaryKey = 'id_doc_vehiculo';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    public const TIPOS = [
        'SOAT',
        'Tecnomecanica',
        'Tarjeta Propiedad',
        'Póliza',
        'Otro'
    ];

    public const ESTADOS = [
        'VIGENTE',
        'POR_VENCER',
        'VENCIDO',
        'REEMPLAZADO'
    ];

    protected $fillable = [
        'id_vehiculo',
        'tipo_documento',
        'numero_documento',
        'entidad_emisora',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'activo',
        'version',
        'reemplazado_por',
        'nota',
        'creado_por',
        'fecha_registro',

    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_registro' => 'datetime',
    ];

    protected $attributes = [
        'activo' => true,
        'estado' => 'VIGENTE',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($documento) {
            if (!$documento->fecha_registro) {
                $documento->fecha_registro = now();
            }

            // si la fecha_vencimiento está presente y estado no fue explicitado
            if ($documento->fecha_vencimiento && empty($documento->estado)) {
                $documento->estado = static::calcularEstado($documento->fecha_vencimiento);
            }
        });

        static::updating(function ($documento) {
            if ($documento->isDirty('fecha_vencimiento') && $documento->fecha_vencimiento) {
                $documento->estado = static::calcularEstado($documento->fecha_vencimiento);
            }
        });
    }

    /**
     * Mutador: normaliza el estado antes de guardar
     *
     * El estado se normaliza a mayúsculas y se reemplaza por 'VIGENTE'
     * si no se encuentra en la lista de ESTADOS.
     *
     * @param string $value
     * @return void
     */
    public function setEstadoAttribute($value)
    {
        $value = strtoupper(str_replace(' ', '_', trim((string) $value)));

        if (in_array($value, self::ESTADOS)) {
            $this->attributes['estado'] = $value;
        } else {
            $this->attributes['estado'] = 'VIGENTE';
        }
    }

    /**
     * Devuelve el estado del documento vehículo como string legible
     *
     * Reemplaza los guiones bajos por espacios para que sea más legible
     *
     * @return string
     */
    public function getEstadoLegibleAttribute()
    {
        return str_replace('_', ' ', $this->estado);
    }

    /**
     * Relación: el documento vehículo pertenece a un vehículo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    // <-- CORRECCIÓN: usar id_doc_vehiculo (según tu esquema)
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_doc_vehiculo', 'id_doc_vehiculo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('estado', 'VIGENTE');
    }

    public function scopePorVencer($query)
    {
        // mejor basarse en fecha_vencimiento si lo prefieres
        $hoy = Carbon::today();
        $proximo = $hoy->copy()->addDays(30);
        return $query->whereBetween('fecha_vencimiento', [$hoy, $proximo])
            ->where('estado', '!=', 'REEMPLAZADO');
    }

    /**
     * Scope a query to only include documents with a expiration date before today
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVencidos($query)
    {
        $hoy = Carbon::today();
        return $query->where(function ($q) use ($hoy) {
            $q->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', $hoy)
                ->orWhere('estado', 'VENCIDO');
        })->where('estado', '!=', 'REEMPLAZADO');
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    public static function calcularEstado($fechaVencimiento)
    {
        if (!$fechaVencimiento) {
            return 'VIGENTE';
        }

        $vencimiento = Carbon::parse($fechaVencimiento);
        $hoy = Carbon::now();
        $diasParaVencer = $hoy->diffInDays($vencimiento, false);

        if ($diasParaVencer < 0) {
            return 'VENCIDO';
        } elseif ($diasParaVencer <= 30) {
            return 'POR_VENCER';
        } else {
            return 'VIGENTE';
        }
    }

    public function estaVigente(): bool
    {
        return $this->estado === 'VIGENTE' && $this->activo;
    }

    public function estaPorVencer(): bool
    {
        return $this->estado === 'POR_VENCER' && $this->activo;
    }

    public function estaVencido(): bool
    {
        return $this->estado === 'VENCIDO';
    }

    public function diasRestantes(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }

        return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getClaseBadge(): string
    {
        return match ($this->estado) {
            'VIGENTE' => 'badge bg-success',
            'POR_VENCER' => 'badge bg-warning text-dark',
            'VENCIDO' => 'badge bg-danger',
            'REEMPLAZADO' => 'badge bg-secondary',
            default => 'badge bg-secondary',
        };
    }

    public function getIcono(): string
    {
        return match ($this->tipo_documento) {
            'SOAT' => 'fa-shield-halved',
            'Tecnomecanica' => 'fa-screwdriver-wrench',
            'Tarjeta Propiedad' => 'fa-id-card',
            'Póliza' => 'fa-file-contract',
            default => 'fa-file',
        };
    }
}
