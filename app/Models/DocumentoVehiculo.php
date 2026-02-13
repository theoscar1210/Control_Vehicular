<?php

namespace App\Models;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\UppercaseFields;

/**
 * Modelo para gestionar documentos de vehículos (SOAT, Tecnomecánica, etc.)
 */
class DocumentoVehiculo extends Model
{
    use HasFactory, LogsActivity, UppercaseFields;

    protected array $uppercaseFields = [
        'tipo_documento', 'numero_documento', 'estado',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('dashboard_stats') ?: Cache::forget('reporte_stats'));
        static::deleted(fn() => Cache::forget('dashboard_stats') ?: Cache::forget('reporte_stats'));
    }

    /**
     * Configuración de auditoría de cambios
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipo_documento', 'numero_documento', 'fecha_vencimiento', 'activo', 'version', 'estado'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Documento {$this->tipo_documento} {$eventName}");
    }

    protected $table = 'documentos_vehiculo';
    protected $primaryKey = 'id_doc_vehiculo';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    public const TIPOS = [
        'SOAT',
        'TECNOMECANICA',
        'TARJETA PROPIEDAD',
        'POLIZA',
        'OTRO'
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



    public function getEstadoAttribute(): string
    {
        // Reemplazado siempre manda
        if (!$this->activo) {
            return 'REEMPLAZADO';
        }

        if (!$this->fecha_vencimiento) {
            return 'VIGENTE';
        }

        $hoy = now()->startOfDay();
        $vence = Carbon::parse($this->fecha_vencimiento)->startOfDay();
        $dias = $hoy->diffInDays($vence, false);


        // Estado basado en días: POR_VENCER si <= 20 días, VIGENTE si > 20 días
        return match (true) {
            $dias < 0 => 'VENCIDO',
            $dias <= 20 => 'POR_VENCER',
            default => 'VIGENTE',
        };
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
        return $this->belongsTo(
            Usuario::class,
            'creado_por',
            'id_usuario'
        )->withDefault([
            'name' => 'Usuario eliminado'
        ]);
    }




    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_doc_vehiculo', 'id_doc_vehiculo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }






    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }





    public function diasRestantes(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }

        return now()->startOfDay()->diffInDays(Carbon::parse($this->fecha_vencimiento)->startOfDay(), false);
    }

    /**
     * Devuelve el color de la clase badge correspondiente al estado del documento vehículo
     * Incluye nivel crítico (0-5 días) que muestra rojo en lugar de amarillo
     *
     * Colores:
     * - danger (rojo): VENCIDO o 0-5 días para vencer (crítico)
     * - warning (amarillo): 6-20 días para vencer
     * - success (verde): más de 20 días para vencer
     * - secondary (gris): reemplazado o sin fecha
     *
     * @return string success|warning|danger|secondary
     */
    public function getClaseBadgeAttribute(): string
    {
        if (!$this->activo) {
            return 'secondary';
        }

        if (!$this->fecha_vencimiento) {
            return 'success';
        }

        $dias = $this->diasRestantes();

        // Rojo: vencido o crítico (0-5 días)
        if ($dias === null || $dias < 0 || $dias <= 5) {
            return 'danger';
        }

        // Amarillo: por vencer (6-20 días)
        if ($dias <= 20) {
            return 'warning';
        }

        // Verde: vigente (más de 20 días)
        return 'success';
    }

    public function getIcono(): string
    {
        return match ($this->tipo_documento) {
            'SOAT' => 'fa-shield-halved',
            'TECNOMECANICA' => 'fa-screwdriver-wrench',
            'TARJETA PROPIEDAD' => 'fa-id-card',
            'POLIZA' => 'fa-file-contract',
            default => 'fa-file',
        };
    }

    public function getTipoDocumentoLabelAttribute(): string
    {
        return ucwords(strtolower($this->tipo_documento ?? ''));
    }
}
