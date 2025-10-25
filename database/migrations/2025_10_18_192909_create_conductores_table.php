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
            //  Clave primaria
            $table->bigIncrements('id_conductor');

            //  Datos del conductor
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('licencia', 50)->unique();
            $table->string('identificacion', 50)->unique()->nullable();

            //  Timestamps automáticos
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
