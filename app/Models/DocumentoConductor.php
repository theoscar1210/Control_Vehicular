<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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

    /**
     * Categorías de Licencia de Conducción en Colombia
     * Según Ley 769 de 2002 y Resolución 3245 de 2009
     */
    public const CATEGORIAS_LICENCIA = [
        'A1' => [
            'nombre' => 'A1 - Motocicletas hasta 125cc',
            'descripcion' => 'Motocicletas, motociclos y mototriciclos con cilindrada hasta 125 c.c.',
            'vigencia_anios' => 10,
            'servicio' => 'particular'
        ],
        'A2' => [
            'nombre' => 'A2 - Motocicletas más de 125cc',
            'descripcion' => 'Motocicletas, motociclos y mototriciclos con cilindrada mayor a 125 c.c.',
            'vigencia_anios' => 10,
            'servicio' => 'particular'
        ],
        'B1' => [
            'nombre' => 'B1 - Automóviles y Camionetas',
            'descripcion' => 'Automóviles, motocarros, cuatrimotos, camperos, camionetas y microbuses hasta 10 pasajeros',
            'vigencia_anios' => 10,
            'servicio' => 'particular'
        ],
        'B2' => [
            'nombre' => 'B2 - Camiones y Buses',
            'descripcion' => 'Camiones rígidos, buses y busetas',
            'vigencia_anios' => 3,
            'servicio' => 'particular'
        ],
        'B3' => [
            'nombre' => 'B3 - Vehículos Articulados',
            'descripcion' => 'Vehículos articulados de carga',
            'vigencia_anios' => 3,
            'servicio' => 'particular'
        ],
        'C1' => [
            'nombre' => 'C1 - Taxi',
            'descripcion' => 'Servicio público individual de pasajeros (taxi)',
            'vigencia_anios' => 3,
            'servicio' => 'publico'
        ],
        'C2' => [
            'nombre' => 'C2 - Bus/Buseta Público',
            'descripcion' => 'Servicio público colectivo de pasajeros (bus, buseta)',
            'vigencia_anios' => 3,
            'servicio' => 'publico'
        ],
        'C3' => [
            'nombre' => 'C3 - Carga Pública',
            'descripcion' => 'Servicio público de carga',
            'vigencia_anios' => 3,
            'servicio' => 'publico'
        ],
    ];

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'id_conductor',
        'tipo_documento',
        'categoria_licencia',
        'categorias_adicionales',
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

    /**
     * Obtener la vigencia en años según la categoría de licencia
     * Según leyes colombianas (Ley 769 de 2002)
     */
    public static function getVigenciaCategoria(string $categoria): int
    {
        if (isset(self::CATEGORIAS_LICENCIA[$categoria])) {
            return self::CATEGORIAS_LICENCIA[$categoria]['vigencia_anios'];
        }
        return 10; // Por defecto 10 años para categorías no especificadas
    }

    /**
     * Calcular fecha de vencimiento basada en categoría y fecha de emisión
     * Considera las reglas especiales por edad del conductor
     */
    public static function calcularFechaVencimiento(
        string $fechaEmision,
        string $categoria,
        ?int $edadConductor = null
    ): string {
        $fecha = Carbon::parse($fechaEmision);
        $vigenciaAnios = self::getVigenciaCategoria($categoria);

        // Reglas especiales por edad (Resolución 217 de 2014)
        if ($edadConductor !== null) {
            if ($edadConductor >= 80) {
                // Mayores de 80 años: vigencia de 1 año
                $vigenciaAnios = 1;
            } elseif ($edadConductor >= 60 && $vigenciaAnios == 10) {
                // Mayores de 60 años con categorías de 10 años: vigencia de 5 años
                $vigenciaAnios = 5;
            }
        }

        return $fecha->addYears($vigenciaAnios)->format('Y-m-d');
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
     */
    public function diasRestantes(): int
    {
        if (!$this->fecha_vencimiento) {
            return 0;
        }
        return Carbon::now()->diffInDays(Carbon::parse($this->fecha_vencimiento), false);
    }
}
