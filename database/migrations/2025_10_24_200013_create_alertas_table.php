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
            $table->bigIncrements('id_alerta');

            //  Llave foránea opcional para documento de vehículo
            $table->unsignedBigInteger('id_documento_vehiculo')->nullable();
            $table->foreign('id_documento_vehiculo')
                ->references('id')
                ->on('documentos_vehiculo')
                ->onDelete('cascade');

            //  Llave foránea opcional para documento de conductor
            $table->unsignedBigInteger('id_documento_conductor')->nullable();
            $table->foreign('id_documento_conductor')
                ->references('id')
                ->on('documentos_conductor')
                ->onDelete('cascade');

            //  Tipo de alerta y estado
            $table->enum('tipo_alerta', ['Proximo a vencer', 'Vencido']);
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->enum('estado', ['Pendiente', 'Atendida'])->default('Pendiente');

            //  Timestamps de Laravel
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
