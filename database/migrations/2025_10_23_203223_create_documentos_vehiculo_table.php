<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_vehiculo');

            //  Relación con la tabla vehiculos
            $table->foreign('id_vehiculo')
                ->references('id_vehiculo')
                ->on('vehiculos')
                ->onDelete('cascade');

            //  Tipo de documento limitado a SOAT o TECNOMECANICA
            $table->enum('tipo_documento', ['SOAT', 'TECNOMECANICA']);

            $table->string('numero_documento', 50)->nullable();
            $table->date('fecha_expedicion')->nullable();
            $table->date('fecha_vencimiento')->nullable();

            // Estado actual del documento
            $table->enum('estado', ['vigente', 'vencido', 'anulado'])->default('vigente');



            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_vehiculo');
    }
};
