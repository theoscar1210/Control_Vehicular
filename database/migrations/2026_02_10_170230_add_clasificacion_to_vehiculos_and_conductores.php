<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clasificaciones actuales: EMPLEADO, CONTRATISTA, FAMILIAR
     *
     * Para agregar mas clasificaciones a futuro (ej: PROVEEDOR, TAXI, MOTOTAXI),
     * crear una nueva migracion que modifique el ENUM:
     *
     * DB::statement("ALTER TABLE vehiculos MODIFY clasificacion
     *     ENUM('EMPLEADO','CONTRATISTA','FAMILIAR','PROVEEDOR','TAXI','MOTOTAXI')
     *     DEFAULT 'EMPLEADO'");
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->enum('clasificacion', ['EMPLEADO', 'CONTRATISTA', 'FAMILIAR'])
                  ->default('EMPLEADO')
                  ->after('estado');
        });

        Schema::table('conductores', function (Blueprint $table) use ($driver) {
            $table->enum('clasificacion', ['EMPLEADO', 'CONTRATISTA', 'FAMILIAR'])
                  ->default('EMPLEADO')
                  ->after('activo');
            $table->unsignedBigInteger('empleado_id')
                  ->nullable()
                  ->after('clasificacion');
            if ($driver === 'mysql') {
                $table->foreign('empleado_id')
                      ->references('id_conductor')
                      ->on('conductores')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('conductores', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->dropForeign(['empleado_id']);
            }
            $table->dropColumn(['clasificacion', 'empleado_id']);
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn('clasificacion');
        });
    }
};
