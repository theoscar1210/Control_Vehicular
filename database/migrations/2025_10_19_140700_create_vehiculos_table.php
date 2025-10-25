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
        Schema::create('vehiculos', function (Blueprint $table) {
            // Clave primaria autoincremental
            $table->bigIncrements('id_vehiculo');

            // Datos del vehículo
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('color', 30)->nullable();
            $table->string('placa', 10)->unique();
            $table->string('numero_licencia_transito', 255)->nullable();

            // Relaciones
            $table->unsignedBigInteger('id_propietario'); // FK a propietarios
            $table->unsignedBigInteger('id_conductor')->nullable(); // FK a conductores

            // Fechas de creación/actualización
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_propietario')
                ->references('id_propietario')
                ->on('propietarios')
                ->onDelete('cascade');


            $table->foreign('id_conductor')
                ->references('id_conductor')
                ->on('conductores')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
