<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Categorías de licencia de conducción en Colombia según la Ley 769 de 2002 y modificaciones.
     *
     * CATEGORÍAS Y VIGENCIA:
     * - A1: Motocicletas hasta 125 cc (10 años)
     * - A2: Motocicletas más de 125 cc (10 años)
     * - B1: Automóviles, motocarros, cuatrimotos, camperos, camionetas (10 años)
     * - B2: Camiones rígidos, buses, busetas (3 años)
     * - B3: Vehículos articulados (3 años)
     * - C1: Servicio público individual (taxi) (3 años)
     * - C2: Servicio público colectivo (bus, buseta) (3 años)
     * - C3: Servicio público de carga (3 años)
     *
     * Para conductores mayores de 60 años: vigencia de 5 años (categorías de 10 años)
     * Para conductores mayores de 80 años: vigencia de 1 año
     */
    public function up(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            // Categoría de la licencia de conducción
            $table->string('categoria_licencia', 10)->nullable()->after('tipo_documento');

            // Múltiples categorías si aplica (ej: "A2,B1,C1")
            $table->string('categorias_adicionales', 50)->nullable()->after('categoria_licencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->dropColumn(['categoria_licencia', 'categorias_adicionales']);
        });
    }
};
