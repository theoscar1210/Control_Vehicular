<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** Nombre explícito de la tabla */
    protected $table = 'usuarios';

    /** Clave primaria personalizada */
    protected $primaryKey = 'id_usuario';

    /** Laravel espera que la clave primaria sea autoincremental y de tipo entero */
    public $incrementing = true;
    protected $keyType = 'int';

    /** Campos que se pueden asignar masivamente */
    protected $fillable = [
        'nombre',
        'apellido',
        'usuario',
        'email',
        'password',
        'rol',
        'activo',
    ];

    /** Campos ocultos al serializar */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Conversión automática de tipos */
    protected $casts = [
        'activo' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Mutador y accesor para el campo "nombre"
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }

    /**
     * Mutador y accesor para el campo "apellido"
     */
    protected function apellido(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }

    /**
     * Relación con la tabla de sesiones (si usas driver database)
     */
    public function sesiones()
    {
        return $this->hasMany(Session::class, 'user_id', 'id_usuario');
    }
}
