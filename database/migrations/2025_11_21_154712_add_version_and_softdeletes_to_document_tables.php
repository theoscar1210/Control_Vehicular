<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVersionAndSoftDeletesToDocumentTables extends Migration
{
    public function up()
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            if (!Schema::hasColumn('documentos_conductor', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('numero_documento');
            }
            if (!Schema::hasColumn('documentos_conductor', 'reemplazado_por')) {
                $table->unsignedBigInteger('reemplazado_por')->nullable()->after('version');
            }
            if (!Schema::hasColumn('documentos_conductor', 'nota')) {
                $table->string('nota', 255)->nullable()->after('reemplazado_por');
            }
            if (!Schema::hasColumn('documentos_conductor', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('documentos_vehiculo', function (Blueprint $table) {
            if (!Schema::hasColumn('documentos_vehiculo', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('numero_documento');
            }
            if (!Schema::hasColumn('documentos_vehiculo', 'reemplazado_por')) {
                $table->unsignedBigInteger('reemplazado_por')->nullable()->after('version');
            }
            if (!Schema::hasColumn('documentos_vehiculo', 'nota')) {
                $table->string('nota', 255)->nullable()->after('reemplazado_por');
            }
            if (!Schema::hasColumn('documentos_vehiculo', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // soft deletes en tablas maestras
        Schema::table('conductores', function (Blueprint $table) {
            if (!Schema::hasColumn('conductores', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            if (!Schema::hasColumn('vehiculos', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('propietarios', function (Blueprint $table) {
            if (!Schema::hasColumn('propietarios', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('alertas', function (Blueprint $table) {
            if (!Schema::hasColumn('alertas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('documentos_conductor', function (Blueprint $table) {
            $table->dropColumn(['version', 'reemplazado_por', 'nota']);
            $table->dropSoftDeletes();
        });
        Schema::table('documentos_vehiculo', function (Blueprint $table) {
            $table->dropColumn(['version', 'reemplazado_por', 'nota']);
            $table->dropSoftDeletes();
        });
        Schema::table('conductores', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('propietarios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
