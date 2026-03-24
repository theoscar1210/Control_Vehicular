<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            //
            $table->date('fecha_matricula')->nullable()->after('modelo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            //
        });
    }
};
