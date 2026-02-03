<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega campo para almacenar qué categorías de licencia deben generar alertas.
     * Permite que un conductor con múltiples categorías solo reciba alertas de las que usa.
     */
    public function up(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->json('categorias_monitoreadas')->nullable()->after('fechas_por_categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->dropColumn('categorias_monitoreadas');
        });
    }
};
