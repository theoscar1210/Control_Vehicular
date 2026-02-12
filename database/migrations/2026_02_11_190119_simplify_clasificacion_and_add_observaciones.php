<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Simplificar clasificaciones: FAMILIAR → EXTERNO
     * Agregar campo observaciones a vehiculos y conductores
     * Eliminar relacion empleado_id (ya no aplica sin FAMILIAR)
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // 1. Actualizar registros existentes: FAMILIAR → EXTERNO
        DB::table('vehiculos')->where('clasificacion', 'FAMILIAR')->update(['clasificacion' => 'EXTERNO']);
        DB::table('conductores')->where('clasificacion', 'FAMILIAR')->update(['clasificacion' => 'EXTERNO']);

        // 2. Modificar ENUM (solo MySQL - SQLite usa TEXT y acepta cualquier valor)
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vehiculos MODIFY clasificacion ENUM('EMPLEADO','EXTERNO','CONTRATISTA') DEFAULT 'EMPLEADO'");
        }

        // 3. Eliminar empleado_id de conductores
        if (Schema::hasColumn('conductores', 'empleado_id')) {
            if ($driver === 'sqlite') {
                // SQLite no puede drop column con FK activa; desactivar temporalmente
                DB::statement('PRAGMA foreign_keys = OFF');
            }
            Schema::table('conductores', function (Blueprint $table) use ($driver) {
                if ($driver === 'mysql') {
                    $table->dropForeign(['empleado_id']);
                }
                $table->dropColumn('empleado_id');
            });
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            }
        }

        // 4. Modificar ENUM en conductores (solo MySQL)
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE conductores MODIFY clasificacion ENUM('EMPLEADO','EXTERNO','CONTRATISTA') DEFAULT 'EMPLEADO'");
        }

        // 5. Agregar campo observaciones
        if (!Schema::hasColumn('vehiculos', 'observaciones')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                $table->text('observaciones')->nullable();
            });
        }

        if (!Schema::hasColumn('conductores', 'observaciones')) {
            Schema::table('conductores', function (Blueprint $table) {
                $table->text('observaciones')->nullable();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('conductores', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });

        DB::table('vehiculos')->where('clasificacion', 'EXTERNO')->update(['clasificacion' => 'FAMILIAR']);
        DB::table('conductores')->where('clasificacion', 'EXTERNO')->update(['clasificacion' => 'FAMILIAR']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vehiculos MODIFY clasificacion ENUM('EMPLEADO','CONTRATISTA','FAMILIAR') DEFAULT 'EMPLEADO'");
            DB::statement("ALTER TABLE conductores MODIFY clasificacion ENUM('EMPLEADO','CONTRATISTA','FAMILIAR') DEFAULT 'EMPLEADO'");
        }

        Schema::table('conductores', function (Blueprint $table) use ($driver) {
            $table->unsignedBigInteger('empleado_id')->nullable();
            if ($driver === 'mysql') {
                $table->foreign('empleado_id')->references('id_conductor')->on('conductores')->onDelete('set null');
            }
        });
    }
};
