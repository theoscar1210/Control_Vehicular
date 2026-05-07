<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_vehiculo')->nullable()->after('id_doc_vehiculo');
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropForeign(['id_vehiculo']);
            $table->dropColumn('id_vehiculo');
        });
    }
};
