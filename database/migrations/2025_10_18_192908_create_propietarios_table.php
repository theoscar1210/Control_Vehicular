<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propietarios', function (Blueprint $table) {
            $table->bigIncrements('id_propietario'); // PK
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('tipo_documento', 15)->nullable();
            $table->string('identificacion', 20)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propietarios');
    }
};
