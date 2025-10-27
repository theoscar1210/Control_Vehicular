<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductores';

    protected $primaryKey = 'id_conductor';
    public $timestamps = false;


    protected $fillable = [
        'nombre',
        'apellidos',
        'tipo_doc',
        'num_doc',
        'telefono',
        'telefono_emergencia',
        'direccion',
        'creado_por',
        'fecha_registro',

    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];


    /**
     * Relación: el conductor tiene muchos vehículos
     * - Usa la clase Vehiculo
     * - Clave foránea en la tabla 'vehiculos': 'id_conductor'
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }
}
