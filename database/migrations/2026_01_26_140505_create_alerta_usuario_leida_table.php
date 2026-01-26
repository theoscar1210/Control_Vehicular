<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivote para rastrear que alertas ha leido cada usuario
     */
    public function up(): void
    {
        Schema::create('alerta_usuario_leida', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alerta');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_lectura')->useCurrent();

            // Indices y claves foraneas
            $table->foreign('id_alerta')->references('id_alerta')->on('alertas')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            // Evitar duplicados: un usuario solo puede marcar una alerta como leida una vez
            $table->unique(['id_alerta', 'id_usuario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerta_usuario_leida');
    }
};
