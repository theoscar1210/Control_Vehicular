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
            // Primary key
            $table->bigIncrements('id_vehiculo');

            // Datos del vehículo
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('color', 30)->nullable();
            $table->string('placa', 10)->unique();

            $table->foreignId('id_empleado_propietario')->constrained('empleados', 'id_empleado')->onDelete('cascade');
            $table->unsignedBigInteger('id_conductor_actual')->nullable();

            $table->timestamps();
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
