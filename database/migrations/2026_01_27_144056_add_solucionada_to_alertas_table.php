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
        Schema::table('alertas', function (Blueprint $table) {
            $table->boolean('solucionada')->default(false)->after('leida');
            $table->timestamp('fecha_solucion')->nullable()->after('solucionada');
            $table->string('motivo_solucion', 100)->nullable()->after('fecha_solucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn(['solucionada', 'fecha_solucion', 'motivo_solucion']);
        });
    }
};
