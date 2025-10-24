<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductores';

    protected $primaryKey = 'id_conductor';

    protected $fillable = [
        'nombre',
        'apellido',
        'licencia',
        'identificacion',
    ];

    /**
     * Relación: el conductor tiene muchos vehículos
     * - Usa la clase Vehiculo
     * - Clave foránea en la tabla 'vehiculos': 'id_conductor'
     */
    public function documentos()
    {
        return $this->hasMany(DocumentoConductor::class, 'id_conductor');
    }
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_conductor');
    }
}
