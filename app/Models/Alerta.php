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

    /**
     * Obtener el nombre de la columna para route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'id_alerta';
    }

    protected $fillable = [
        'tipo_alerta',
        'id_doc_vehiculo',
        'id_doc_conductor',
        'tipo_vencimiento',
        'mensaje',
        'fecha_alerta',
        'leida',
        'solucionada',
        'fecha_solucion',
        'motivo_solucion',
        'visible_para',
        'creado_por',
        'fecha_registro',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'solucionada' => 'boolean',
        'fecha_alerta' => 'date',
        'fecha_solucion' => 'datetime',
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

    /**
     * Relacion: usuarios que han leido esta alerta
     * Parametros: modelo, tabla_pivote, fk_alerta, fk_usuario, pk_local, pk_relacionado
     */
    public function usuariosQueLeyeron()
    {
        return $this->belongsToMany(
            Usuario::class,
            'alerta_usuario_leida',
            'id_alerta',      // FK de Alerta en tabla pivote
            'id_usuario',     // FK de Usuario en tabla pivote
            'id_alerta',      // PK local (Alerta)
            'id_usuario'      // PK del modelo relacionado (Usuario)
        )->withPivot('fecha_lectura');
    }

    /**
     * Verificar si un usuario especifico ha leido esta alerta
     * Usa la relación cargada si está disponible para evitar consultas N+1
     */
    public function leidaPorUsuario($userId): bool
    {
        // Asegurar que userId sea entero para comparación correcta
        $userId = (int) $userId;

        // Si la relación ya está cargada (eager loading), usar la colección
        if ($this->relationLoaded('usuariosQueLeyeron')) {
            return $this->usuariosQueLeyeron->pluck('id_usuario')->contains($userId);
        }

        // Si no está cargada, hacer la consulta
        return $this->usuariosQueLeyeron()->where('alerta_usuario_leida.id_usuario', $userId)->exists();
    }

    /**
     * Marcar como leida para un usuario especifico
     */
    public function marcarLeidaPara($userId): void
    {
        $userId = (int) $userId;

        // Verificar directamente en la base de datos
        $yaLeida = \DB::table('alerta_usuario_leida')
            ->where('id_alerta', $this->id_alerta)
            ->where('id_usuario', $userId)
            ->exists();

        if (!$yaLeida) {
            \DB::table('alerta_usuario_leida')->insert([
                'id_alerta' => $this->id_alerta,
                'id_usuario' => $userId,
                'fecha_lectura' => now(),
            ]);
        }
    }

    /**
     * Scope para obtener alertas no leidas por un usuario
     */
    public function scopeNoLeidasPor($query, $userId)
    {
        return $query->whereDoesntHave('usuariosQueLeyeron', function ($q) use ($userId) {
            $q->where('alerta_usuario_leida.id_usuario', $userId);
        });
    }

    /**
     * Scope para obtener alertas leidas por un usuario
     */
    public function scopeLeidasPor($query, $userId)
    {
        return $query->whereHas('usuariosQueLeyeron', function ($q) use ($userId) {
            $q->where('alerta_usuario_leida.id_usuario', $userId);
        });
    }

    /**
     * Scope para obtener solo alertas activas (no solucionadas)
     */
    public function scopeActivas($query)
    {
        return $query->where('solucionada', false);
    }

    /**
     * Scope para obtener alertas solucionadas
     */
    public function scopeSolucionadas($query)
    {
        return $query->where('solucionada', true);
    }

    /**
     * Marcar alerta como solucionada (documento renovado)
     */
    public function marcarComoSolucionada(string $motivo = 'DOCUMENTO_RENOVADO'): void
    {
        $this->update([
            'solucionada' => true,
            'fecha_solucion' => now(),
            'motivo_solucion' => $motivo,
        ]);
    }

    /**
     * Marcar todas las alertas de un documento de vehículo como solucionadas
     */
    public static function solucionarPorDocumentoVehiculo(int $idDocVehiculo, string $motivo = 'DOCUMENTO_RENOVADO'): int
    {
        return self::where('id_doc_vehiculo', $idDocVehiculo)
            ->where('solucionada', false)
            ->update([
                'solucionada' => true,
                'fecha_solucion' => now(),
                'motivo_solucion' => $motivo,
            ]);
    }

    /**
     * Marcar todas las alertas de un documento de conductor como solucionadas
     */
    public static function solucionarPorDocumentoConductor(int $idDocConductor, string $motivo = 'DOCUMENTO_RENOVADO'): int
    {
        return self::where('id_doc_conductor', $idDocConductor)
            ->where('solucionada', false)
            ->update([
                'solucionada' => true,
                'fecha_solucion' => now(),
                'motivo_solucion' => $motivo,
            ]);
    }

    /**
     * Generar alertas para un documento de conductor si está vencido o próximo a vencer.
     * Este método se debe llamar después de crear o actualizar un documento.
     *
     * @param DocumentoConductor $documento El documento a evaluar
     * @return int Número de alertas creadas
     */
    public static function generarAlertasDocumentoConductor(DocumentoConductor $documento): int
    {
        $hoy = \Carbon\Carbon::today();
        $proximo = $hoy->copy()->addDays(15);
        $creadas = 0;

        // Si es una licencia de conducción con fechas por categoría
        if ($documento->tipo_documento === 'Licencia Conducción' && !empty($documento->fechas_por_categoria)) {
            $categoriasAMonitorear = $documento->getCategoriasAMonitorear();

            foreach ($categoriasAMonitorear as $categoria) {
                $fechaVencimiento = null;
                $fechasPorCategoria = $documento->fechas_por_categoria;

                if (isset($fechasPorCategoria[$categoria]['fecha_vencimiento'])) {
                    $fechaVencimiento = \Carbon\Carbon::parse($fechasPorCategoria[$categoria]['fecha_vencimiento']);
                }

                if (!$fechaVencimiento) {
                    continue;
                }

                // Verificar si está vencida o próxima a vencer
                $necesitaAlerta = $fechaVencimiento->lt($hoy) ||
                    ($fechaVencimiento->gte($hoy) && $fechaVencimiento->lte($proximo));

                if (!$necesitaAlerta) {
                    continue;
                }

                $tipo_v = $fechaVencimiento->lt($hoy) ? 'VENCIDO' : 'PROXIMO_VENCER';
                $mensajeBusqueda = "Licencia categoría {$categoria}";

                // Verificar si ya existe una alerta para esta categoría
                $alertaExistente = self::where('tipo_vencimiento', $tipo_v)
                    ->whereNull('deleted_at')
                    ->where('id_doc_conductor', $documento->id_doc_conductor)
                    ->where('mensaje', 'like', "%{$mensajeBusqueda}%")
                    ->where('solucionada', false)
                    ->exists();

                if ($alertaExistente) {
                    continue;
                }

                $nombreConductor = $documento->conductor
                    ? "{$documento->conductor->nombre} {$documento->conductor->apellido}"
                    : 'Conductor desconocido';

                $mensaje = sprintf("Licencia categoría %s (%s) - %s - vence: %s",
                    $categoria,
                    $documento->numero_documento,
                    $nombreConductor,
                    $fechaVencimiento->format('Y-m-d')
                );

                self::create([
                    'tipo_alerta' => 'CONDUCTOR',
                    'id_doc_vehiculo' => null,
                    'id_doc_conductor' => $documento->id_doc_conductor,
                    'tipo_vencimiento' => $tipo_v,
                    'mensaje' => $mensaje,
                    'fecha_alerta' => \Carbon\Carbon::today(),
                    'leida' => 0,
                    'solucionada' => false,
                    'visible_para' => 'TODOS',
                    'creado_por' => null,
                ]);
                $creadas++;
            }
        } else {
            // Para otros documentos o licencias sin fechas por categoría
            if (!$documento->fecha_vencimiento) {
                return 0;
            }

            $fechaVencimiento = \Carbon\Carbon::parse($documento->fecha_vencimiento);

            $necesitaAlerta = $fechaVencimiento->lt($hoy) ||
                ($fechaVencimiento->gte($hoy) && $fechaVencimiento->lte($proximo));

            if (!$necesitaAlerta) {
                return 0;
            }

            $tipo_v = $fechaVencimiento->lt($hoy) ? 'VENCIDO' : 'PROXIMO_VENCER';

            // Verificar si ya existe una alerta
            $alertaExistente = self::where('tipo_vencimiento', $tipo_v)
                ->whereNull('deleted_at')
                ->where('id_doc_conductor', $documento->id_doc_conductor)
                ->where('solucionada', false)
                ->exists();

            if ($alertaExistente) {
                return 0;
            }

            $nombreConductor = $documento->conductor
                ? "{$documento->conductor->nombre} {$documento->conductor->apellido}"
                : 'Conductor desconocido';

            $mensaje = sprintf("Documento %s (%s) - %s - vence: %s",
                $documento->tipo_documento,
                $documento->numero_documento,
                $nombreConductor,
                $fechaVencimiento->format('Y-m-d')
            );

            self::create([
                'tipo_alerta' => 'CONDUCTOR',
                'id_doc_vehiculo' => null,
                'id_doc_conductor' => $documento->id_doc_conductor,
                'tipo_vencimiento' => $tipo_v,
                'mensaje' => $mensaje,
                'fecha_alerta' => \Carbon\Carbon::today(),
                'leida' => 0,
                'solucionada' => false,
                'visible_para' => 'TODOS',
                'creado_por' => null,
            ]);
            $creadas++;
        }

        return $creadas;
    }
}
