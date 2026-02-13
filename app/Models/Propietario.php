<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\UppercaseFields;

class Propietario extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, UppercaseFields;

    protected array $uppercaseFields = [
        'nombre', 'apellido', 'tipo_doc', 'identificacion',
    ];

    protected $table = 'propietarios';
    protected $primaryKey = 'id_propietario';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_doc',
        'identificacion',
        'creado_por',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Configuración de auditoría de cambios
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'apellido', 'tipo_doc', 'identificacion'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Propietario {$eventName}");
    }

    // relaciones
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_propietario', 'id_propietario');
    }

    /**
     * Relación: el propietario tiene un vehículo
     * - Usa la clase 'vehiculo'
     * - Clave foránea en la tabla 'vehiculos': 'id_propietario'
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
