<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivote para relación muchos a muchos entre conductores y vehículos.
     * Un vehículo puede tener varios conductores asignados.
     * Un conductor puede manejar varios vehículos.
     */
    public function up(): void
    {
        Schema::create('conductor_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_conductor');
            $table->unsignedBigInteger('id_vehiculo');
            $table->boolean('es_principal')->default(false)->comment('Indica si es el conductor principal del vehículo');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->timestamp('fecha_desasignacion')->nullable();
            $table->boolean('activo')->default(true)->comment('Si la asignación está activa');

            // Claves foráneas
            $table->foreign('id_conductor')->references('id_conductor')->on('conductores')->onDelete('cascade');
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');

            // Índice compuesto para búsquedas rápidas
            $table->index(['id_conductor', 'id_vehiculo', 'activo'], 'conductor_vehiculo_idx');
        });

        // Migrar datos existentes de la columna id_conductor en vehiculos a la tabla pivote
        DB::statement("
            INSERT INTO conductor_vehiculo (id_conductor, id_vehiculo, es_principal, activo)
            SELECT id_conductor, id_vehiculo, 1, 1
            FROM vehiculos
            WHERE id_conductor IS NOT NULL AND deleted_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conductor_vehiculo');
    }
};
