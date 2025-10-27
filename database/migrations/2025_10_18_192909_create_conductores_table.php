<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conductores', function (Blueprint $table) {
            $table->id('id_conductor');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('tipo_doc', ['CC', 'CE']);
            $table->string('identificacion', 50)->unique();
            $table->string('telefono', 30)->nullable();
            $table->string('telefono_emergencia', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->foreign('creado_por')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
