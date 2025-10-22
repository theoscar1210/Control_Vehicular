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
        Schema::create('conductores', function (Blueprint $table) {
            $table->id('id_conductor');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('licencia', 50)->unique();
            $table->string('identificacion', 50)->nullable();
            $table->enum('tipo', ['Empleado', 'Externo'])->default('Externo');
            $table->foreignId('id_empleado')->nullable()->constrained('empleados', 'id_empleado')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
