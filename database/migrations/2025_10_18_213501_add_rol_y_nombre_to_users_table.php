<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolYNombreToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Añadimos nombre y rol; colocamos valores por defecto/nullable para evitar errores si ya hay datos
            if (!Schema::hasColumn('users', 'nombre')) {
                $table->string('nombre', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'rol')) {
                $table->enum('rol', ['SST', 'Seguridad', 'Administrador'])
                    ->default('SST')
                    ->after('email');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos las columnas si existen (rollback)
            if (Schema::hasColumn('users', 'rol')) {
                $table->dropColumn('rol');
            }
            if (Schema::hasColumn('users', 'nombre')) {
                $table->dropColumn('nombre');
            }
        });
    }
}
