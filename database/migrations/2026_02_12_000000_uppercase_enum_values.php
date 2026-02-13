<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalizar valores ENUM a UPPERCASE en todas las tablas.
     * MySQL: Actualiza datos + redefine ENUM.
     * SQLite: Solo actualiza datos (TEXT acepta cualquier valor).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // 1. Actualizar datos existentes a UPPERCASE
        // vehiculos.tipo: Carro -> CARRO, Moto -> MOTO, etc.
        DB::table('vehiculos')->where('tipo', 'Carro')->update(['tipo' => 'CARRO']);
        DB::table('vehiculos')->where('tipo', 'Moto')->update(['tipo' => 'MOTO']);
        DB::table('vehiculos')->where('tipo', 'Camion')->update(['tipo' => 'CAMION']);
        DB::table('vehiculos')->where('tipo', 'Otro')->update(['tipo' => 'OTRO']);

        // vehiculos.estado: Activo -> ACTIVO, Inactivo -> INACTIVO
        DB::table('vehiculos')->where('estado', 'Activo')->update(['estado' => 'ACTIVO']);
        DB::table('vehiculos')->where('estado', 'Inactivo')->update(['estado' => 'INACTIVO']);

        // documentos_vehiculo.tipo_documento
        DB::table('documentos_vehiculo')->where('tipo_documento', 'Tecnomecanica')->update(['tipo_documento' => 'TECNOMECANICA']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'Tarjeta Propiedad')->update(['tipo_documento' => 'TARJETA PROPIEDAD']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'Póliza')->update(['tipo_documento' => 'POLIZA']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'Poliza_Seguro')->update(['tipo_documento' => 'POLIZA']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'Otro')->update(['tipo_documento' => 'OTRO']);

        // documentos_conductor.tipo_documento
        DB::table('documentos_conductor')->where('tipo_documento', 'Licencia Conducción')->update(['tipo_documento' => 'LICENCIA CONDUCCION']);
        DB::table('documentos_conductor')->where('tipo_documento', 'Licencia Conduccion')->update(['tipo_documento' => 'LICENCIA CONDUCCION']);
        DB::table('documentos_conductor')->where('tipo_documento', 'Certificado Médico')->update(['tipo_documento' => 'CERTIFICADO MEDICO']);
        DB::table('documentos_conductor')->where('tipo_documento', 'Certificado Medico')->update(['tipo_documento' => 'CERTIFICADO MEDICO']);
        DB::table('documentos_conductor')->where('tipo_documento', 'Otro')->update(['tipo_documento' => 'OTRO']);

        // Uppercase nombres, apellidos, placas en tablas existentes
        if ($driver === 'mysql') {
            DB::statement('UPDATE vehiculos SET placa = UPPER(placa), marca = UPPER(marca), modelo = UPPER(COALESCE(modelo, "")), color = UPPER(COALESCE(color, ""))');
            DB::statement('UPDATE conductores SET nombre = UPPER(nombre), apellido = UPPER(apellido)');
            DB::statement('UPDATE propietarios SET nombre = UPPER(nombre), apellido = UPPER(apellido)');
        } else {
            // SQLite
            DB::statement('UPDATE vehiculos SET placa = UPPER(placa), marca = UPPER(marca)');
            DB::statement('UPDATE conductores SET nombre = UPPER(nombre), apellido = UPPER(apellido)');
            DB::statement('UPDATE propietarios SET nombre = UPPER(nombre), apellido = UPPER(apellido)');
        }

        // 2. Redefinir ENUM en MySQL
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vehiculos MODIFY tipo ENUM('CARRO','MOTO','CAMION','OTRO') NOT NULL");
            DB::statement("ALTER TABLE vehiculos MODIFY estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'");
            DB::statement("ALTER TABLE documentos_vehiculo MODIFY tipo_documento ENUM('SOAT','TECNOMECANICA','TARJETA PROPIEDAD','POLIZA','OTRO') NOT NULL");
            DB::statement("ALTER TABLE documentos_conductor MODIFY tipo_documento ENUM('LICENCIA CONDUCCION','EPS','ARL','CERTIFICADO MEDICO','OTRO') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Revertir datos
        DB::table('vehiculos')->where('tipo', 'CARRO')->update(['tipo' => 'Carro']);
        DB::table('vehiculos')->where('tipo', 'MOTO')->update(['tipo' => 'Moto']);
        DB::table('vehiculos')->where('tipo', 'CAMION')->update(['tipo' => 'Camion']);
        DB::table('vehiculos')->where('tipo', 'OTRO')->update(['tipo' => 'Otro']);
        DB::table('vehiculos')->where('estado', 'ACTIVO')->update(['estado' => 'Activo']);
        DB::table('vehiculos')->where('estado', 'INACTIVO')->update(['estado' => 'Inactivo']);

        DB::table('documentos_vehiculo')->where('tipo_documento', 'TECNOMECANICA')->update(['tipo_documento' => 'Tecnomecanica']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'TARJETA PROPIEDAD')->update(['tipo_documento' => 'Tarjeta Propiedad']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'POLIZA')->update(['tipo_documento' => 'Póliza']);
        DB::table('documentos_vehiculo')->where('tipo_documento', 'OTRO')->update(['tipo_documento' => 'Otro']);

        DB::table('documentos_conductor')->where('tipo_documento', 'LICENCIA CONDUCCION')->update(['tipo_documento' => 'Licencia Conducción']);
        DB::table('documentos_conductor')->where('tipo_documento', 'CERTIFICADO MEDICO')->update(['tipo_documento' => 'Certificado Médico']);
        DB::table('documentos_conductor')->where('tipo_documento', 'OTRO')->update(['tipo_documento' => 'Otro']);

        // Revertir ENUM en MySQL
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vehiculos MODIFY tipo ENUM('Carro','Moto','Camion','Otro') NOT NULL");
            DB::statement("ALTER TABLE vehiculos MODIFY estado ENUM('Activo','Inactivo') DEFAULT 'Activo'");
            DB::statement("ALTER TABLE documentos_vehiculo MODIFY tipo_documento ENUM('SOAT','Tecnomecanica','Tarjeta Propiedad','Póliza','Otro') NOT NULL");
            DB::statement("ALTER TABLE documentos_conductor MODIFY tipo_documento ENUM('Licencia Conducción','EPS','ARL','Certificado Médico','Otro') NOT NULL");
        }
    }
};
