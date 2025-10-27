<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id('id_alerta');
            $table->enum('tipo_alerta', ['VEHICULO', 'CONDUCTOR']);
            $table->unsignedBigInteger('id_doc_vehiculo')->nullable();
            $table->unsignedBigInteger('id_doc_conductor')->nullable();
            $table->enum('tipo_vencimiento', ['VENCIDO', 'PROXIMO_VENCER']);
            $table->string('mensaje', 255)->nullable();
            $table->date('fecha_alerta')->nullable();
            $table->boolean('leida')->default(false);
            $table->enum('visible_para', ['ADMIN', 'SST', 'PORTERIA', 'TODOS'])->default('TODOS');
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_doc_vehiculo')->references('id_doc_vehiculo')->on('documentos_vehiculo')->onDelete('set null');
            $table->foreign('id_doc_conductor')->references('id_doc_conductor')->on('documentos_conductor')->onDelete('set null');
            $table->foreign('creado_por')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
