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

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'apellido',
        'usuario',
        'email',
        'password',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'password' => 'hashed',
        'create_at' => 'datetime',
        'update_at' => 'datetime',
    ];

    /** Mutadores */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? ucfirst($value) : null,
            set: fn($value) => $value ? strtolower($value) : null,
        );
    }

    protected function apellido(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }
    // ejemplo de relación: usuarios pueden crear registros 
    public function creados()
    {
        // relación polimórfica/ genérica
        return $this->hasMany(Propietario::class, 'creado_por', 'id_usuario');
    }
}
