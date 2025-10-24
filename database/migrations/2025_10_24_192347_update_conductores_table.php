<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conductores', function (Blueprint $table) {
            // Primero elimina la FK si existe
            $table->dropForeign(['id_empleado']);

            // Luego elimina las columnas
            $table->dropColumn(['id_empleado', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::table('conductores', function (Blueprint $table) {
            //  Restaurar columnas si se hace rollback
            $table->unsignedBigInteger('id_empleado')->nullable();
            $table->enum('tipo', ['Empleado', 'Externo'])->default('Externo');

            //  Restaurar la FK si existía
            $table->foreign('id_empleado')
                ->references('id_empleado')
                ->on('empleados')
                ->onDelete('set null');
        });
    }
};
