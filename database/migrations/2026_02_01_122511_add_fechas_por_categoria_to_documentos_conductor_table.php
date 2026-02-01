<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar columna JSON para almacenar fechas de emisión y vencimiento
     * por cada categoría de licencia de conducción.
     *
     * Estructura del JSON:
     * {
     *   "B1": {"fecha_emision": "2024-01-15", "fecha_vencimiento": "2034-01-15"},
     *   "A2": {"fecha_emision": "2023-05-10", "fecha_vencimiento": "2033-05-10"}
     * }
     *
     * La fecha_vencimiento principal del documento será la más próxima a vencer.
     */
    public function up(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->json('fechas_por_categoria')->nullable()->after('categorias_adicionales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->dropColumn('fechas_por_categoria');
        });
    }
};
