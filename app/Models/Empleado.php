<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    //
    protected $primaryKey = 'id_empleado';
    protected $fillables = ['nombre', 'apellido', 'identificacion', 'estado', 'email', 'telefono', 'direccion', ''];
}
