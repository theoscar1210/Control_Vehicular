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
        Schema::create('alertas', function (Blueprint $table) {
            $table->id('id_alerta');
            $table->foreignId('id_documento')->constrained('documentos', 'id_documento')->onDelete('cascade');
            $table->enum('tipo_alerta', ['Proximo a vencer', 'vencido']);
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->enum('estado', ['Pendiente', 'Atendida'])->default('Pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
