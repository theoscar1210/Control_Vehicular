<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\UppercaseFields;

class DocumentoConductor extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, UppercaseFields;

    protected array $uppercaseFields = [
        'tipo_documento', 'numero_documento', 'estado', 'entidad_emisora', 'categoria_licencia', 'categorias_adicionales',
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
            ->logOnly(['tipo_documento', 'numero_documento', 'categoria_licencia', 'fecha_vencimiento', 'activo', 'version'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Documento conductor {$this->tipo_documento} {$eventName}");
    }

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'documentos_conductor';
    protected $primaryKey = 'id_doc_conductor';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    /** Constantes para tipos y estados */
    public const TIPOS = ['LICENCIA CONDUCCION', 'EPS', 'ARL', 'CERTIFICADO MEDICO', 'OTRO'];
    public const ESTADOS = ['VIGENTE', 'POR_VENCER', 'VENCIDO', 'REEMPLAZADO'];

    /**
     * Categorías de Licencia de Conducción en Colombia
     * Según Ley 769 de 2002 y Resolución 3245 de 2009
     * Nota: La vigencia depende de la edad del conductor, por lo que
     * el usuario debe ingresar la fecha de vencimiento manualmente.
     */
    public const CATEGORIAS_LICENCIA = [
        'A1' => [
            'nombre' => 'A1 - Motocicletas hasta 125cc',
            'descripcion' => 'Motocicletas, motociclos y mototriciclos con cilindrada hasta 125 c.c.',
            'servicio' => 'particular'
        ],
        'A2' => [
            'nombre' => 'A2 - Motocicletas más de 125cc',
            'descripcion' => 'Motocicletas, motociclos y mototriciclos con cilindrada mayor a 125 c.c.',
            'servicio' => 'particular'
        ],
        'B1' => [
            'nombre' => 'B1 - Automóviles y Camionetas',
            'descripcion' => 'Automóviles, motocarros, cuatrimotos, camperos, camionetas y microbuses hasta 10 pasajeros',
            'servicio' => 'particular'
        ],
        'B2' => [
            'nombre' => 'B2 - Camiones y Buses',
            'descripcion' => 'Camiones rígidos, buses y busetas',
            'servicio' => 'particular'
        ],
        'B3' => [
            'nombre' => 'B3 - Vehículos Articulados',
            'descripcion' => 'Vehículos articulados de carga',
            'servicio' => 'particular'
        ],
        'C1' => [
            'nombre' => 'C1 - Taxi',
            'descripcion' => 'Servicio público individual de pasajeros (taxi)',
            'servicio' => 'publico'
        ],
        'C2' => [
            'nombre' => 'C2 - Bus/Buseta Público',
            'descripcion' => 'Servicio público colectivo de pasajeros (bus, buseta)',
            'servicio' => 'publico'
        ],
        'C3' => [
            'nombre' => 'C3 - Carga Pública',
            'descripcion' => 'Servicio público de carga',
            'servicio' => 'publico'
        ],
    ];

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'id_conductor',
        'tipo_documento',
        'categoria_licencia',
        'categorias_adicionales',
        'fechas_por_categoria',
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
        'fecha_registro',
        'categorias_monitoreadas'
    ];

    /** Conversión automática de tipos de datos */
    protected $casts = [
        'activo' => 'boolean',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_registro' => 'datetime',
        'fechas_por_categoria' => 'array',
        'categorias_monitoreadas' => 'array',
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
     * Accessor: calcula el estado dinámicamente basado en fecha de vencimiento
     * Esto asegura que el estado siempre esté actualizado
     */
    public function getEstadoAttribute(): string
    {
        // Si no está activo, está reemplazado
        if (!$this->activo) {
            return 'REEMPLAZADO';
        }

        // Si no tiene fecha de vencimiento, está vigente
        if (!$this->fecha_vencimiento) {
            return 'VIGENTE';
        }

        $hoy = Carbon::today();
        $vence = Carbon::parse($this->fecha_vencimiento)->startOfDay();
        $dias = (int) $hoy->diffInDays($vence, false);

        // Estado basado en días: VENCIDO si < 0, POR_VENCER si <= 20, VIGENTE si > 20
        return match (true) {
            $dias < 0 => 'VENCIDO',
            $dias <= 20 => 'POR_VENCER',
            default => 'VIGENTE',
        };
    }

    /**
     * Obtener estado legible para mostrar en la interfaz
     */
    public function getEstadoLegibleAttribute(): string
    {
        return str_replace('_', ' ', $this->estado);
    }

    /**
     * Obtener clase CSS de Bootstrap para el badge del estado
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
        if ($dias < 0 || $dias <= 5) {
            return 'danger';
        }

        // Amarillo: por vencer (6-20 días)
        if ($dias <= 20) {
            return 'warning';
        }

        // Verde: vigente (más de 20 días)
        return 'success';
    }

    /**
     * Obtener todas las categorías disponibles para select
     */
    public static function getCategoriasParaSelect(): array
    {
        $categorias = [];
        foreach (self::CATEGORIAS_LICENCIA as $codigo => $info) {
            $categorias[$codigo] = $info['nombre'];
        }
        return $categorias;
    }

    /**
     * Obtener información completa de una categoría
     */
    public static function getInfoCategoria(string $categoria): ?array
    {
        return self::CATEGORIAS_LICENCIA[$categoria] ?? null;
    }

    /**
     * Obtener nombre de la categoría
     */
    public function getNombreCategoriaAttribute(): string
    {
        if ($this->categoria_licencia && isset(self::CATEGORIAS_LICENCIA[$this->categoria_licencia])) {
            return self::CATEGORIAS_LICENCIA[$this->categoria_licencia]['nombre'];
        }
        return $this->categoria_licencia ?? 'Sin categoría';
    }

    /**
     * Obtener todas las categorías del conductor (principal + adicionales)
     */
    public function getTodasCategoriasAttribute(): array
    {
        $categorias = [];

        if ($this->categoria_licencia) {
            $categorias[] = $this->categoria_licencia;
        }

        if ($this->categorias_adicionales) {
            $adicionales = explode(',', $this->categorias_adicionales);
            $categorias = array_merge($categorias, array_map('trim', $adicionales));
        }

        return array_unique($categorias);
    }

    /**
     * Calcular días restantes hasta el vencimiento
     * Usa startOfDay() para evitar decimales y asegurar consistencia
     */
    public function diasRestantes(): int
    {
        if (!$this->fecha_vencimiento) {
            return 0;
        }
        return (int) Carbon::today()->diffInDays(
            Carbon::parse($this->fecha_vencimiento)->startOfDay(),
            false
        );
    }

    /**
     * Obtener la fecha de vencimiento de una categoría específica
     */
    public function getVencimientoCategoria(string $categoria): ?string
    {
        $fechas = $this->fechas_por_categoria;
        return $fechas[$categoria]['fecha_vencimiento'] ?? null;
    }

    /**
     * Establecer fecha de vencimiento para una categoría específica
     * (La fecha de emisión es única para toda la licencia)
     */
    public function setVencimientoCategoria(string $categoria, ?string $fechaVencimiento): void
    {
        $fechas = $this->fechas_por_categoria ?? [];
        $fechas[$categoria] = [
            'fecha_vencimiento' => $fechaVencimiento,
        ];
        $this->fechas_por_categoria = $fechas;
    }

    /**
     * Calcular la fecha de vencimiento más próxima de todas las categorías
     * Esta será usada como fecha_vencimiento principal del documento
     */
    public function calcularFechaVencimientoMasProxima(): ?Carbon
    {
        $fechas = $this->fechas_por_categoria;

        if (empty($fechas)) {
            return $this->fecha_vencimiento ? Carbon::parse($this->fecha_vencimiento) : null;
        }

        $fechaMinima = null;

        foreach ($fechas as $categoria => $data) {
            if (!empty($data['fecha_vencimiento'])) {
                $fecha = Carbon::parse($data['fecha_vencimiento']);
                if ($fechaMinima === null || $fecha->lt($fechaMinima)) {
                    $fechaMinima = $fecha;
                }
            }
        }

        return $fechaMinima;
    }

    /**
     * Obtener la categoría con el vencimiento más próximo
     */
    public function getCategoriaProximaAVencer(): ?string
    {
        $fechas = $this->fechas_por_categoria;

        if (empty($fechas)) {
            return $this->categoria_licencia;
        }

        $categoriaMinima = null;
        $fechaMinima = null;

        foreach ($fechas as $categoria => $data) {
            if (!empty($data['fecha_vencimiento'])) {
                $fecha = Carbon::parse($data['fecha_vencimiento']);
                if ($fechaMinima === null || $fecha->lt($fechaMinima)) {
                    $fechaMinima = $fecha;
                    $categoriaMinima = $categoria;
                }
            }
        }

        return $categoriaMinima;
    }

    /**
     * Obtener las categorías que deben generar alertas de vencimiento.
     * Si no hay categorías configuradas, retorna todas las categorías del documento.
     * Esto asegura retrocompatibilidad con documentos existentes.
     *
     * @return array Lista de códigos de categoría (ej: ['B1', 'C1'])
     */
    public function getCategoriasAMonitorear(): array
    {
        // Si hay categorías monitoreadas configuradas, usarlas
        if (!empty($this->categorias_monitoreadas)) {
            return $this->categorias_monitoreadas;
        }

        // Fallback: retornar todas las categorías (comportamiento anterior)
        return $this->todas_categorias;
    }

    /**
     * Verificar si una categoría específica debe ser monitoreada para alertas
     *
     * @param string $categoria Código de categoría (ej: 'B1')
     * @return bool
     */
    public function debeMonitorearCategoria(string $categoria): bool
    {
        return in_array($categoria, $this->getCategoriasAMonitorear());
    }

    public function getTipoDocumentoLabelAttribute(): string
    {
        return ucwords(strtolower($this->tipo_documento ?? ''));
    }
}
