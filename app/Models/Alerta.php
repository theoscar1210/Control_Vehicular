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
        'visible_para',
        'creado_por',
        'fecha_registro',

    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_alerta' => 'date',
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
}
