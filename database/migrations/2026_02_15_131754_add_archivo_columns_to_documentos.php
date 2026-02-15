<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos_vehiculo', function (Blueprint $table) {
            $table->string('ruta_archivo')->nullable();
            $table->string('google_drive_file_id')->nullable();
        });

        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->string('ruta_archivo')->nullable();
            $table->string('google_drive_file_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('documentos_vehiculo', function (Blueprint $table) {
            $table->dropColumn(['ruta_archivo', 'google_drive_file_id']);
        });

        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->dropColumn(['ruta_archivo', 'google_drive_file_id']);
        });
    }
};
