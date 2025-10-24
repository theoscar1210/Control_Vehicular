<?php

// Define el espacio de nombres donde vive este modelo
namespace App\Models;

// Importa funcionalidades necesarias para el modelo
use Illuminate\Database\Eloquent\Factories\HasFactory; // Para generar datos de prueba (factories)
use Illuminate\Foundation\Auth\User as Authenticatable; // Clase base para modelos de usuario autenticables
use Illuminate\Notifications\Notifiable; // Permite enviar notificaciones al usuario
use Illuminate\Database\Eloquent\Casts\Attribute; // Para definir mutadores y accesores personalizados
use laravel\Sanctum\HasApiTokens; // Permite manejar tokens de autenticación con Laravel Sanctum

// Define el modelo User, que extiende la clase Authenticatable
class User extends Authenticatable
{
    // Usa traits para añadir funcionalidades al modelo
    use HasApiTokens, HasFactory, Notifiable;

    /** Nombre explícito de la tabla en la base de datos */
    protected $table = 'users';

    /** Campos que se pueden asignar masivamente (por ejemplo, en create() o update()) */
    protected $fillable = [
        'nombre',           // Nombre del usuario
        'email',            // Correo electrónico
        'password',         // Contraseña (se espera que esté hasheada)
        'rol',              // Rol del usuario (Administrador, SST, etc.)
    ];

    /** Campos que se ocultan al serializar el modelo (por ejemplo, al convertirlo a JSON) */
    protected $hidden = [
        'password',         // No se debe exponer la contraseña
        'remember_token',   // Token de "recordarme" en sesiones
    ];

    /** Conversión automática de tipos de datos */
    protected $casts = [
        'email_verified_at' => 'datetime', // Convierte el campo a objeto DateTime
        'password' => 'hashed',            // Laravel encripta automáticamente este campo al asignarlo
    ];

    /**
     * Mutador y accesor personalizado para el campo "nombre"
     * - get: capitaliza la primera letra al obtener el valor
     * - set: convierte todo a minúsculas al guardar el valor
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),      // Ej: "oscar" → "Oscar"
            set: fn($value) => strtolower($value),   // Ej: "OSCAR" → "oscar"
        );
    }
}
