<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de usuarios con clave primaria personalizada
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // Clave primaria personalizada
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('usuario', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('rol', ['ADMIN', 'SST', 'PORTERIA'])->default('PORTERIA')->index(); // Índice para filtros
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Tabla para recuperación de contraseñas (solo si no usas Fortify)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabla de sesiones (driver database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            // Relación explícita con usuarios.id_usuario
            $table->foreign('user_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar primero las tablas dependientes
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
