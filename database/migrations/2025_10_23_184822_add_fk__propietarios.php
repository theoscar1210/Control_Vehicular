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
        Schema::table('propietarios', function (Blueprint $table) {
            if (!Schema::hasColumn('propietarios', 'id_vehiculo')) {
                $table->unsignedBigInteger('id_vehiculo')->nullable()->after('identificacion');
                $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
