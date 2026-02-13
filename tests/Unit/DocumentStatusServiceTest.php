<?php

namespace Tests\Unit;

use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Services\DocumentStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentStatusService();
    }

    // ─── calcularEstadoGeneral ───────────────────────────────────────

    public function test_estado_general_sin_documentos(): void
    {
        $vehiculo = Vehiculo::factory()->create();
        $vehiculo->load('documentos');

        $resultado = $this->service->calcularEstadoGeneral($vehiculo);

        $this->assertEquals('SIN_DOCUMENTOS', $resultado['estado']);
        $this->assertEquals('secondary', $resultado['clase']);
    }

    public function test_estado_general_vigente(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-001',
            'fecha_emision' => Carbon::now()->subMonths(3),
            'fecha_vencimiento' => Carbon::now()->addMonths(9),
            'activo' => true,
            'version' => 1,
        ]);

        $vehiculo->load('documentos');

        $resultado = $this->service->calcularEstadoGeneral($vehiculo);

        $this->assertEquals('VIGENTE', $resultado['estado']);
        $this->assertEquals('success', $resultado['clase']);
    }

    public function test_estado_general_por_vencer(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-002',
            'fecha_emision' => Carbon::now()->subMonths(11),
            'fecha_vencimiento' => Carbon::now()->addDays(10),
            'activo' => true,
            'version' => 1,
        ]);

        $vehiculo->load('documentos');

        $resultado = $this->service->calcularEstadoGeneral($vehiculo);

        $this->assertEquals('POR_VENCER', $resultado['estado']);
        $this->assertEquals('warning', $resultado['clase']);
    }

    public function test_estado_general_vencido(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-003',
            'fecha_emision' => Carbon::now()->subYears(2),
            'fecha_vencimiento' => Carbon::now()->subDays(30),
            'activo' => true,
            'version' => 1,
        ]);

        $vehiculo->load('documentos');

        $resultado = $this->service->calcularEstadoGeneral($vehiculo);

        $this->assertEquals('VENCIDO', $resultado['estado']);
        $this->assertEquals('danger', $resultado['clase']);
    }

    public function test_estado_general_prioriza_vencido_sobre_por_vencer(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Un documento vencido
        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-V',
            'fecha_vencimiento' => Carbon::now()->subDays(5),
            'activo' => true,
            'version' => 1,
        ]);

        // Otro documento por vencer
        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'TECNOMECANICA',
            'numero_documento' => 'TM-PV',
            'fecha_vencimiento' => Carbon::now()->addDays(10),
            'activo' => true,
            'version' => 1,
        ]);

        $vehiculo->load('documentos');

        $resultado = $this->service->calcularEstadoGeneral($vehiculo);

        $this->assertEquals('VENCIDO', $resultado['estado']);
    }

    // ─── calcularEstadosDetallados ───────────────────────────────────

    public function test_estados_detallados_sin_registro(): void
    {
        $vehiculo = Vehiculo::factory()->create();
        $vehiculo->load(['documentos', 'conductor.documentosConductor']);

        $estados = $this->service->calcularEstadosDetallados($vehiculo);

        $this->assertEquals('SIN_REGISTRO', $estados['vehiculo_SOAT']['estado']);
        $this->assertEquals('secondary', $estados['vehiculo_SOAT']['clase']);
        $this->assertEquals('No registrado', $estados['vehiculo_SOAT']['mensaje']);
    }

    public function test_estados_detallados_con_documentos(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-DET',
            'fecha_emision' => Carbon::now()->subMonths(3),
            'fecha_vencimiento' => Carbon::now()->addMonths(9),
            'activo' => true,
            'version' => 1,
        ]);

        $vehiculo->load(['documentos', 'conductor.documentosConductor']);

        $estados = $this->service->calcularEstadosDetallados($vehiculo);

        $this->assertEquals('VIGENTE', $estados['vehiculo_SOAT']['estado']);
        $this->assertEquals('success', $estados['vehiculo_SOAT']['clase']);
        $this->assertNotNull($estados['vehiculo_SOAT']['documento']);
        // Tecnomecanica sin registro
        $this->assertEquals('SIN_REGISTRO', $estados['vehiculo_TECNOMECANICA']['estado']);
    }

    // ─── getClaseEstado ──────────────────────────────────────────────

    public function test_clase_estado_con_dias(): void
    {
        $this->assertEquals('danger', $this->service->getClaseEstado('VENCIDO', -5));
        $this->assertEquals('danger', $this->service->getClaseEstado('POR_VENCER', 3));
        $this->assertEquals('warning', $this->service->getClaseEstado('POR_VENCER', 15));
        $this->assertEquals('success', $this->service->getClaseEstado('VIGENTE', 50));
    }

    public function test_clase_estado_sin_dias(): void
    {
        $this->assertEquals('success', $this->service->getClaseEstado('VIGENTE'));
        $this->assertEquals('warning', $this->service->getClaseEstado('POR_VENCER'));
        $this->assertEquals('danger', $this->service->getClaseEstado('VENCIDO'));
        $this->assertEquals('secondary', $this->service->getClaseEstado('SIN_REGISTRO'));
    }

    // ─── getMensajeEstado ────────────────────────────────────────────

    public function test_mensaje_estado_vigente(): void
    {
        $mensaje = $this->service->getMensajeEstado('VIGENTE', 90);
        $this->assertStringContainsString('Vigente', $mensaje);
        $this->assertStringContainsString('90', $mensaje);
    }

    public function test_mensaje_estado_vencido(): void
    {
        $mensaje = $this->service->getMensajeEstado('VENCIDO', -10);
        $this->assertStringContainsString('Vencido', $mensaje);
        $this->assertStringContainsString('10', $mensaje);
    }

    public function test_mensaje_estado_sin_dias(): void
    {
        $mensaje = $this->service->getMensajeEstado('VIGENTE');
        $this->assertEquals('Estado desconocido', $mensaje);
    }
}
