<?php

namespace Tests\Unit;

use App\Models\Alerta;
use App\Models\Conductor;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Services\AlertaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlertaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertaService();
    }

    // =========================================================================
    // evaluarDocumentoVehiculo
    // =========================================================================

    public function test_genera_alerta_documento_vehiculo_vencido(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'estado' => 'VENCIDO',
        ])->create();

        $alerta = $this->service->evaluarDocumentoVehiculo($doc);

        $this->assertNotNull($alerta);
        $this->assertEquals('VEHICULO', $alerta->tipo_alerta);
        $this->assertEquals('VENCIDO', $alerta->tipo_vencimiento);
        $this->assertEquals($doc->id_doc_vehiculo, $alerta->id_doc_vehiculo);
    }

    public function test_genera_alerta_documento_vehiculo_proximo_a_vencer(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(10)->format('Y-m-d'),
            'estado' => 'POR_VENCER',
        ])->create();

        $alerta = $this->service->evaluarDocumentoVehiculo($doc);

        $this->assertNotNull($alerta);
        $this->assertEquals('PROXIMO_VENCER', $alerta->tipo_vencimiento);
    }

    public function test_no_genera_alerta_documento_vehiculo_vigente(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(60)->format('Y-m-d'),
            'estado' => 'VIGENTE',
        ])->create();

        $alerta = $this->service->evaluarDocumentoVehiculo($doc);

        $this->assertNull($alerta);
    }

    public function test_no_genera_alerta_sin_fecha_vencimiento(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'Tarjeta Propiedad',
            'fecha_vencimiento' => null,
            'estado' => 'VIGENTE',
        ])->create();

        $alerta = $this->service->evaluarDocumentoVehiculo($doc);

        $this->assertNull($alerta);
    }

    public function test_no_duplica_alerta_vehiculo(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'estado' => 'VENCIDO',
        ])->create();

        $alerta1 = $this->service->evaluarDocumentoVehiculo($doc);
        $alerta2 = $this->service->evaluarDocumentoVehiculo($doc);

        $this->assertNotNull($alerta1);
        $this->assertNull($alerta2);
        $this->assertEquals(1, Alerta::where('id_doc_vehiculo', $doc->id_doc_vehiculo)->count());
    }

    // =========================================================================
    // evaluarDocumentoConductor
    // =========================================================================

    public function test_genera_alerta_documento_conductor_vencido(): void
    {
        $conductor = Conductor::factory()->create();
        $doc = DocumentoConductor::factory()->state([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => 'EPS',
            'fecha_vencimiento' => Carbon::today()->subDays(3)->format('Y-m-d'),
            'estado' => 'VENCIDO',
        ])->create();
        $doc->load('conductor');

        $creadas = $this->service->evaluarDocumentoConductor($doc);

        $this->assertEquals(1, $creadas);
        $this->assertDatabaseHas('alertas', [
            'tipo_alerta' => 'CONDUCTOR',
            'id_doc_conductor' => $doc->id_doc_conductor,
            'tipo_vencimiento' => 'VENCIDO',
        ]);
    }

    public function test_no_genera_alerta_conductor_vigente(): void
    {
        $conductor = Conductor::factory()->create();
        $doc = DocumentoConductor::factory()->state([
            'id_conductor' => $conductor->id_conductor,
            'tipo_documento' => 'EPS',
            'fecha_vencimiento' => Carbon::today()->addDays(60)->format('Y-m-d'),
            'estado' => 'VIGENTE',
        ])->create();
        $doc->load('conductor');

        $creadas = $this->service->evaluarDocumentoConductor($doc);

        $this->assertEquals(0, $creadas);
    }

    // =========================================================================
    // solucionarPorRenovacion
    // =========================================================================

    public function test_solucionar_alertas_al_renovar_vehiculo(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'estado' => 'VENCIDO',
        ])->create();

        // Crear alerta
        $this->service->evaluarDocumentoVehiculo($doc);
        $this->assertEquals(1, Alerta::where('id_doc_vehiculo', $doc->id_doc_vehiculo)->where('solucionada', false)->count());

        // Solucionar
        $solucionadas = $this->service->solucionarPorRenovacionVehiculo($doc->id_doc_vehiculo);

        $this->assertEquals(1, $solucionadas);
        $this->assertEquals(0, Alerta::where('id_doc_vehiculo', $doc->id_doc_vehiculo)->where('solucionada', false)->count());
    }

    // =========================================================================
    // procesarBatch
    // =========================================================================

    public function test_procesar_batch_vehiculos(): void
    {
        // Documento vencido - debe generar alerta
        DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(2)->format('Y-m-d'),
            'estado' => 'VENCIDO',
            'reemplazado_por' => null,
        ])->create();

        // Documento vigente - no debe generar alerta
        DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(100)->format('Y-m-d'),
            'estado' => 'VIGENTE',
            'reemplazado_por' => null,
        ])->create();

        $resultado = $this->service->procesarDocumentosVehiculosBatch();

        $this->assertEquals(1, $resultado['revisados']);
        $this->assertEquals(1, $resultado['creadas']);
    }
}
