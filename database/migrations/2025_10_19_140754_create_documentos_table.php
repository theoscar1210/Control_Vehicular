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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento');
            $table->enum('tipo', ['SOAT', 'tecnomecanica', 'licencia']);
            $table->string('numero', 100)->nullable();
            $table->date('fecha_expedicion');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['vigente', 'vencido', 'proximo_a_vencer'])->default('Vigente');
            $table->unsignedBigInteger('id_vehiculo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
