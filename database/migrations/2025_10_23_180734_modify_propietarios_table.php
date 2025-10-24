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
            if (Schema::hasColumn('propietarios', 'id_empleado')) {
                $table->renameColumn('id_empleado', 'id_propietario');
            }


            if (! Schema::hasColumn('propietarios', 'tipo_documento')) {
                $table->string('tipo_documento', 15)->nullable()->after('apellido');
            }

            $drop = [];
            if (Schema::hasColumn('propietarios', 'area')) {

                $drop[] = 'area';
            }
            if (Schema::hasColumn('propietarios', 'estado')) {

                $drop[] = 'estado';
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propietarios', function (Blueprint $table) {
            //
        });
    }
};
