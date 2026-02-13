<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos_conductor', function (Blueprint $table) {
            $table->id('id_doc_conductor');
            $table->unsignedBigInteger('id_conductor');
            $table->enum('tipo_documento', ['LICENCIA CONDUCCION', 'EPS', 'ARL', 'CERTIFICADO MEDICO', 'OTRO']);
            $table->string('numero_documento', 50);
            $table->string('entidad_emisora', 100)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['VIGENTE', 'POR_VENCER', 'VENCIDO', 'REEMPLAZADO'])->default('VIGENTE');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();


            $table->foreign('id_conductor')->references('id_conductor')->on('conductores')->onDelete('cascade');
            $table->foreign('creado_por')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_conductor');
    }
};
