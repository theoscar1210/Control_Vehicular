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
        Schema::create('historial_documentos', function (Blueprint $table) {
            $table->id('id_historial');
            $table->foreignId('id_documento')->constrained('documentos', 'id_documento')->onDelete('cascade');
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('accion', 50);
            $table->foreignId('usuario_responsable')->nullable()->constrained('users', 'id')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_documentos');
    }
};
