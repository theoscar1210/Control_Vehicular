<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductores';
    public $incrementing = true;
    protected $primaryKey = 'id_conductor';
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
     * Relación: el conductor tiene muchos vehículos
     * - Usa la clase Vehiculo
     * - Clave foránea en la tabla 'vehiculos': 'id_conductor'
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_conductor', 'id_conductor');
    }

    public function documentosConductor()
    {
        return $this->hasMany(DocumentoConductor::class, 'id_conductor', 'id_conductor');
    }


    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
