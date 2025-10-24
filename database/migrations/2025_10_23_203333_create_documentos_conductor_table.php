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
        Schema::create('documentos_conductor', function (Blueprint $table) {
            $table->id();
            // Relación correcta con la tabla conductores
            $table->unsignedBigInteger('id_conductor');
            $table->foreign('id_conductor')
                ->references('id_conductor')
                ->on('conductores')
                ->onDelete('cascade');
            //  Tipo de documento limitado a licencia de conduccion
            $table->string('tipo_documento', 50);
            $table->string('numero_documento', 50)->nullable();
            $table->date('fecha_expedicion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['vigente', 'vencido', 'anulado'])->default('vigente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_conductor');
    }
};
