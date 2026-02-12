<?php

namespace Tests\Unit;

use App\Models\Alerta;
use App\Models\DocumentoVehiculo;
use App\Models\Vehiculo;
use App\Services\DocumentoVehiculoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoVehiculoServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentoVehiculoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DocumentoVehiculoService::class);
    }

    // =========================================================================
    // crearDocumento
    // =========================================================================

    public function test_crear_documento_soat_vigente(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $doc = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-12345',
            'entidad_emisora' => 'Seguros S.A.',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $this->assertNotNull($doc);
        $this->assertEquals('SOAT', $doc->tipo_documento);
        $this->assertEquals('VIGENTE', $doc->estado);
        $this->assertEquals(1, $doc->version);
        $this->assertEquals(1, $doc->activo);

        // Vencimiento a 1 anio
        $this->assertEquals(
            Carbon::today()->addYear()->format('Y-m-d'),
            Carbon::parse($doc->fecha_vencimiento)->format('Y-m-d')
        );
    }

    public function test_crear_documento_tarjeta_propiedad_sin_vencimiento(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $doc = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'Tarjeta Propiedad',
            'numero_documento' => 'TP-999',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $this->assertNull($doc->fecha_vencimiento);
        $this->assertEquals('VIGENTE', $doc->estado);
    }

    public function test_versionamiento_incrementa_al_crear_mismo_tipo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $doc1 = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-001',
            'fecha_emision' => Carbon::today()->subYear()->format('Y-m-d'),
        ]);

        $doc2 = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-002',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $this->assertEquals(1, $doc1->version);
        $this->assertEquals(2, $doc2->version);

        // Documento anterior marcado como reemplazado
        $doc1->refresh();
        $this->assertEquals('REEMPLAZADO', $doc1->estado);
        $this->assertEquals(0, $doc1->activo);
        $this->assertEquals($doc2->id_doc_vehiculo, $doc1->reemplazado_por);
    }

    public function test_crear_documento_vencido_genera_alerta(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $doc = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-VENCIDO',
            'fecha_emision' => Carbon::today()->subMonths(14)->format('Y-m-d'),
        ]);

        $this->assertEquals('VENCIDO', $doc->estado);
        $this->assertDatabaseHas('alertas', [
            'tipo_alerta' => 'VEHICULO',
            'id_doc_vehiculo' => $doc->id_doc_vehiculo,
            'tipo_vencimiento' => 'VENCIDO',
        ]);
    }

    public function test_crear_documento_vigente_no_genera_alerta(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $doc = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-VIGENTE',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $this->assertEquals('VIGENTE', $doc->estado);
        $this->assertDatabaseMissing('alertas', [
            'id_doc_vehiculo' => $doc->id_doc_vehiculo,
        ]);
    }

    public function test_reemplazar_documento_soluciona_alertas_anteriores(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear documento vencido (genera alerta)
        $docAnterior = $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-VIEJO',
            'fecha_emision' => Carbon::today()->subMonths(14)->format('Y-m-d'),
        ]);

        $alertaAnterior = Alerta::where('id_doc_vehiculo', $docAnterior->id_doc_vehiculo)->first();
        $this->assertNotNull($alertaAnterior);
        $this->assertFalse($alertaAnterior->solucionada);

        // Crear nuevo documento (reemplaza y soluciona)
        $this->service->crearDocumento($vehiculo, [
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'S-NUEVO',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $alertaAnterior->refresh();
        $this->assertTrue($alertaAnterior->solucionada);
        $this->assertEquals('DOCUMENTO_RENOVADO', $alertaAnterior->motivo_solucion);
    }

    // =========================================================================
    // renovarDocumento
    // =========================================================================

    public function test_renovar_documento_vencido(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear con fecha_vencimiento pasada para que el accessor compute VENCIDO
        $docAnterior = DocumentoVehiculo::factory()->state([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'version' => 1,
            'activo' => 1,
            'reemplazado_por' => null,
        ])->create();

        // Verificar que el accessor computa VENCIDO
        $this->assertEquals('VENCIDO', $docAnterior->estado);

        $nuevoDoc = $this->service->renovarDocumento($vehiculo, $docAnterior, [
            'numero_documento' => 'S-RENOVADO',
            'entidad_emisora' => 'Seguros S.A.',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);

        $this->assertEquals(2, $nuevoDoc->version);
        $this->assertEquals('VIGENTE', $nuevoDoc->estado);
        $this->assertEquals('SOAT', $nuevoDoc->tipo_documento);

        $docAnterior->refresh();
        $this->assertEquals('REEMPLAZADO', $docAnterior->estado);
    }

    public function test_renovar_documento_vigente_lanza_excepcion(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear con fecha_vencimiento lejana para que el accessor compute VIGENTE
        $docVigente = DocumentoVehiculo::factory()->state([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(100)->format('Y-m-d'),
            'version' => 1,
            'activo' => 1,
        ])->create();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->renovarDocumento($vehiculo, $docVigente, [
            'numero_documento' => 'S-TEST',
            'fecha_emision' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    // =========================================================================
    // esRenovable / requiereVencimiento
    // =========================================================================

    public function test_es_renovable_soat_vencido(): void
    {
        // Usar fecha_vencimiento pasada para que el accessor compute VENCIDO
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->subDays(10)->format('Y-m-d'),
            'activo' => 1,
        ])->create();

        $this->assertEquals('VENCIDO', $doc->estado);
        $this->assertTrue($this->service->esRenovable($doc));
    }

    public function test_es_renovable_soat_por_vencer(): void
    {
        // Usar fecha_vencimiento proxima para que el accessor compute POR_VENCER
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(10)->format('Y-m-d'),
            'activo' => 1,
        ])->create();

        $this->assertEquals('POR_VENCER', $doc->estado);
        $this->assertTrue($this->service->esRenovable($doc));
    }

    public function test_no_es_renovable_tarjeta_propiedad(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'Tarjeta Propiedad',
            'fecha_vencimiento' => null,
            'activo' => 1,
        ])->create();

        $this->assertFalse($this->service->esRenovable($doc));
    }

    public function test_no_es_renovable_soat_vigente(): void
    {
        $doc = DocumentoVehiculo::factory()->state([
            'tipo_documento' => 'SOAT',
            'fecha_vencimiento' => Carbon::today()->addDays(100)->format('Y-m-d'),
            'activo' => 1,
        ])->create();

        $this->assertEquals('VIGENTE', $doc->estado);
        $this->assertFalse($this->service->esRenovable($doc));
    }

    public function test_requiere_vencimiento(): void
    {
        $this->assertTrue($this->service->requiereVencimiento('SOAT'));
        $this->assertTrue($this->service->requiereVencimiento('Tecnomecanica'));
        $this->assertFalse($this->service->requiereVencimiento('Tarjeta Propiedad'));
    }
}
