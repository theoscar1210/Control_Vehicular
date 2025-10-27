<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id('id_vehiculo');
            $table->string('placa', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->enum('tipo', ['Carro', 'Moto', 'Camion', 'Otro']);
            $table->unsignedBigInteger('id_propietario');
            $table->unsignedBigInteger('id_conductor')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->foreign('id_propietario')->references('id_propietario')->on('propietarios')->onDelete('cascade');
            $table->foreign('id_conductor')->references('id_conductor')->on('conductores')->onDelete('set null');
            $table->foreign('creado_por')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
