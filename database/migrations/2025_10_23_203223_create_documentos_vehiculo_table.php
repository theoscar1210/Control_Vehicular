<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos_vehiculo', function (Blueprint $table) {
            $table->id('id_doc_vehiculo');
            $table->unsignedBigInteger('id_vehiculo');
            $table->enum('tipo_documento', ['SOAT', 'TECNOMECANICA', 'TARJETA PROPIEDAD', 'POLIZA', 'OTRO']);
            $table->string('numero_documento', 50);
            $table->string('entidad_emisora', 100)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['VIGENTE', 'POR_VENCER', 'VENCIDO', 'REEMPLAZADO'])->default('VIGENTE');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');
            $table->foreign('creado_por')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_vehiculo');
    }
};
