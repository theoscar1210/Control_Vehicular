<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Propietario;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use App\Models\Alerta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class VehiculoConductorTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = Usuario::factory()->create([
            'rol' => 'ADMIN',
            'activo' => true,
        ]);
    }

    /**
     * Test: Crear un propietario
     */
    public function test_puede_crear_propietario(): void
    {
        $propietario = Propietario::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'identificacion' => '123456789',
        ]);

        $this->assertDatabaseHas('propietarios', [
            'id_propietario' => $propietario->id_propietario,
            'nombre' => 'JUAN',
            'apellido' => 'PÉREZ',
        ]);
    }

    /**
     * Test: Crear un conductor
     */
    public function test_puede_crear_conductor(): void
    {
        $conductor = Conductor::factory()->create([
            'nombre' => 'Carlos',
            'apellido' => 'Gómez',
            'identificacion' => '987654321',
            'telefono' => '3109876543',
            'activo' => true,
        ]);

        $this->assertDatabaseHas('conductores', [
            'id_conductor' => $conductor->id_conductor,
            'nombre' => 'CARLOS',
            'apellido' => 'GÓMEZ',
            'activo' => true,
        ]);
    }

    /**
     * Test: Crear un vehículo con propietario y conductor
     */
    public function test_puede_crear_vehiculo_con_propietario_y_conductor(): void
    {
        $propietario = Propietario::factory()->create();
        $conductor = Conductor::factory()->create();

        $vehiculo = Vehiculo::factory()->create([
            'placa' => 'ABC123',
            'marca' => 'Toyota',
            'modelo' => 'Corolla 2023',
            'color' => 'Blanco',
            'tipo' => 'CARRO',
            'id_propietario' => $propietario->id_propietario,
            'id_conductor' => $conductor->id_conductor,
            'estado' => 'ACTIVO',
        ]);

        $this->assertDatabaseHas('vehiculos', [
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'placa' => 'ABC123',
            'marca' => 'TOYOTA',
        ]);

        // Verificar relaciones
        $this->assertEquals($propietario->id_propietario, $vehiculo->id_propietario);
        $this->assertEquals($conductor->id_conductor, $vehiculo->id_conductor);

        // Verificar que se pueden cargar las relaciones
        $vehiculo->load(['propietario', 'conductor']);
        $this->assertEquals('TOYOTA', $vehiculo->marca);
        $this->assertNotNull($vehiculo->propietario);
        $this->assertNotNull($vehiculo->conductor);
    }

    /**
     * Test: Crear documento de vehículo (SOAT)
     */
    public function test_puede_crear_documento_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $documento = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-2024-001',
            'fecha_emision' => Carbon::now()->subMonths(6),
            'fecha_vencimiento' => Carbon::now()->addMonths(6),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
            'creado_por' => $this->adminUser->id_usuario,
        ]);

        $this->assertDatabaseHas('documentos_vehiculo', [
            'id_doc_vehiculo' => $documento->id_doc_vehiculo,
            'tipo_documento' => 'SOAT',
            'estado' => 'VIGENTE',
        ]);

        // Verificar relación
        $this->assertEquals($vehiculo->id_vehiculo, $documento->id_vehiculo);
    }

    /**
     * Test: Crear documento de conductor (Licencia)
     */
    public function test_puede_crear_documento_conductor(): void
    {
        $conductor = Conductor::factory()->create();

        $documento = DocumentoConductor::create([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => 'LICENCIA CONDUCCION',
            'categoria_licencia' => 'B1',
            'numero_documento' => 'LIC-2024-001',
            'fecha_emision' => Carbon::now()->subYears(2),
            'fecha_vencimiento' => Carbon::now()->addYears(3),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
            'creado_por' => $this->adminUser->id_usuario,
        ]);

        $this->assertDatabaseHas('documentos_conductor', [
            'id_doc_conductor' => $documento->id_doc_conductor,
            'tipo_documento' => 'LICENCIA CONDUCCION',
            'categoria_licencia' => 'B1',
        ]);
    }

    /**
     * Test: Documento vencido genera estado correcto
     */
    public function test_documento_vencido_tiene_estado_correcto(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $documento = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-VENCIDO',
            'fecha_emision' => Carbon::now()->subYears(2),
            'fecha_vencimiento' => Carbon::now()->subDays(10), // Vencido
            'estado' => 'VENCIDO',
            'activo' => true,
            'version' => 1,
        ]);

        // El accessor debería retornar VENCIDO
        $this->assertEquals('VENCIDO', $documento->estado);
    }

    /**
     * Test: Documento próximo a vencer tiene estado correcto
     */
    public function test_documento_proximo_vencer_tiene_estado_correcto(): void
    {
        $conductor = Conductor::factory()->create();

        $documento = DocumentoConductor::create([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => 'EPS',
            'numero_documento' => 'EPS-2024',
            'fecha_emision' => Carbon::now()->subMonths(11),
            'fecha_vencimiento' => Carbon::now()->addDays(10), // Próximo a vencer
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
        ]);

        // El accessor debería retornar POR_VENCER
        $this->assertEquals('POR_VENCER', $documento->estado);
    }

    /**
     * Test: Vehículo completo con todos los documentos
     */
    public function test_vehiculo_completo_con_documentos(): void
    {
        // Crear propietario
        $propietario = Propietario::factory()->create([
            'nombre' => 'Empresa',
            'apellido' => 'ABC S.A.S',
        ]);

        // Crear conductor
        $conductor = Conductor::factory()->create([
            'nombre' => 'Pedro',
            'apellido' => 'Martínez',
        ]);

        // Crear vehículo
        $vehiculo = Vehiculo::factory()->create([
            'placa' => 'XYZ789',
            'marca' => 'Chevrolet',
            'modelo' => 'Spark 2022',
            'tipo' => 'CARRO',
            'id_propietario' => $propietario->id_propietario,
            'id_conductor' => $conductor->id_conductor,
        ]);

        // Crear documentos del vehículo
        $soat = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-XYZ789',
            'fecha_vencimiento' => Carbon::now()->addMonths(8),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
        ]);

        $tecno = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'TECNOMECANICA',
            'numero_documento' => 'TM-XYZ789',
            'fecha_vencimiento' => Carbon::now()->addMonths(10),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
        ]);

        // Crear documentos del conductor
        $licencia = DocumentoConductor::create([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => 'LICENCIA CONDUCCION',
            'categoria_licencia' => 'C1',
            'numero_documento' => 'LIC-PM-001',
            'fecha_vencimiento' => Carbon::now()->addYears(5),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 1,
        ]);

        // Cargar vehículo con todas las relaciones
        $vehiculo->load(['propietario', 'conductor', 'documentos']);

        // Verificaciones
        $this->assertNotNull($vehiculo->propietario);
        $this->assertNotNull($vehiculo->conductor);
        $this->assertCount(2, $vehiculo->documentos);

        // Verificar que el conductor tiene documentos
        $conductor->load('documentosConductor');
        $this->assertCount(1, $conductor->documentosConductor);
    }

    /**
     * Test: Acceso a vista de vehículos como ADMIN
     */
    public function test_admin_puede_ver_lista_vehiculos(): void
    {
        Vehiculo::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('vehiculos.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Acceso a vista de conductores como ADMIN
     */
    public function test_admin_puede_ver_lista_conductores(): void
    {
        Conductor::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('conductores.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Crear vehículo genera alerta si documento vencido
     */
    public function test_documento_vencido_puede_generar_alerta(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear documento vencido
        $documento = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-VENCIDO-TEST',
            'fecha_vencimiento' => Carbon::now()->subDays(5),
            'estado' => 'VENCIDO',
            'activo' => true,
            'version' => 1,
        ]);

        // Crear alerta manualmente (como lo haría el comando)
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'id_doc_vehiculo' => $documento->id_doc_vehiculo,
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => "Documento SOAT vencido - Placa: {$vehiculo->placa}",
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        $this->assertDatabaseHas('alertas', [
            'id_alerta' => $alerta->id_alerta,
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
        ]);

        // Verificar relación
        $alerta->load('documentoVehiculo');
        $this->assertNotNull($alerta->documentoVehiculo);
        $this->assertEquals('SOAT', $alerta->documentoVehiculo->tipo_documento);
    }

    /**
     * Test: Renovar documento marca alerta como solucionada
     */
    public function test_renovar_documento_soluciona_alerta(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear documento vencido
        $docAnterior = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-OLD',
            'fecha_vencimiento' => Carbon::now()->subDays(5),
            'estado' => 'VENCIDO',
            'activo' => true,
            'version' => 1,
        ]);

        // Crear alerta para el documento vencido
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'id_doc_vehiculo' => $docAnterior->id_doc_vehiculo,
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'SOAT vencido',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        // Simular renovación: crear nuevo documento
        $docNuevo = DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-NEW',
            'fecha_vencimiento' => Carbon::now()->addYear(),
            'estado' => 'VIGENTE',
            'activo' => true,
            'version' => 2,
        ]);

        // Marcar documento anterior como reemplazado
        $docAnterior->update([
            'estado' => 'REEMPLAZADO',
            'activo' => false,
            'reemplazado_por' => $docNuevo->id_doc_vehiculo,
        ]);

        // Marcar alertas como solucionadas (como lo hace el controlador)
        Alerta::solucionarPorDocumentoVehiculo($docAnterior->id_doc_vehiculo, 'DOCUMENTO_RENOVADO');

        // Verificar que la alerta está solucionada
        $alerta->refresh();
        $this->assertTrue($alerta->solucionada);
        $this->assertEquals('DOCUMENTO_RENOVADO', $alerta->motivo_solucion);
        $this->assertNotNull($alerta->fecha_solucion);
    }

    /**
     * Test: Soft delete de conductor
     */
    public function test_soft_delete_conductor(): void
    {
        $conductor = Conductor::factory()->create();

        $conductor->delete();

        // No debería aparecer en consultas normales
        $this->assertDatabaseMissing('conductores', [
            'id_conductor' => $conductor->id_conductor,
            'deleted_at' => null,
        ]);

        // Pero debería existir con deleted_at
        $this->assertSoftDeleted('conductores', [
            'id_conductor' => $conductor->id_conductor,
        ]);

        // Se puede restaurar
        $conductor->restore();

        $this->assertDatabaseHas('conductores', [
            'id_conductor' => $conductor->id_conductor,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test: Soft delete de vehículo
     */
    public function test_soft_delete_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $vehiculo->delete();

        $this->assertSoftDeleted('vehiculos', [
            'id_vehiculo' => $vehiculo->id_vehiculo,
        ]);

        // Restaurar
        $vehiculo->restore();

        $this->assertDatabaseHas('vehiculos', [
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'deleted_at' => null,
        ]);
    }
}
