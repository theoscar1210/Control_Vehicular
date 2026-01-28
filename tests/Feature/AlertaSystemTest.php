<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\DocumentoVehiculo;
use App\Models\DocumentoConductor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class AlertaSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $adminUser;
    protected Usuario $sstUser;
    protected Usuario $porteriaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuarios de prueba
        $this->adminUser = Usuario::factory()->create([
            'rol' => 'ADMIN',
            'activo' => true,
        ]);

        $this->sstUser = Usuario::factory()->create([
            'rol' => 'SST',
            'activo' => true,
        ]);

        $this->porteriaUser = Usuario::factory()->create([
            'rol' => 'PORTERIA',
            'activo' => true,
        ]);
    }

    /**
     * Test: Crear una alerta
     */
    public function test_puede_crear_alerta(): void
    {
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Documento SOAT vencido',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        $this->assertDatabaseHas('alertas', [
            'id_alerta' => $alerta->id_alerta,
            'tipo_alerta' => 'VEHICULO',
            'solucionada' => false,
        ]);
    }

    /**
     * Test: Marcar alerta como leída para usuario específico
     */
    public function test_marcar_alerta_como_leida_por_usuario(): void
    {
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Test alerta',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        // Marcar como leída para admin
        $alerta->marcarLeidaPara($this->adminUser->id_usuario);

        // Verificar que admin la ve como leída
        $this->assertTrue($alerta->leidaPorUsuario($this->adminUser->id_usuario));

        // Verificar que SST NO la ve como leída
        $this->assertFalse($alerta->leidaPorUsuario($this->sstUser->id_usuario));

        // Verificar que PORTERIA NO la ve como leída
        $this->assertFalse($alerta->leidaPorUsuario($this->porteriaUser->id_usuario));
    }

    /**
     * Test: Marcar alerta como solucionada
     */
    public function test_marcar_alerta_como_solucionada(): void
    {
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Test alerta',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        $alerta->marcarComoSolucionada('DOCUMENTO_RENOVADO');

        $this->assertDatabaseHas('alertas', [
            'id_alerta' => $alerta->id_alerta,
            'solucionada' => true,
            'motivo_solucion' => 'DOCUMENTO_RENOVADO',
        ]);
    }

    /**
     * Test: Scope activas filtra alertas solucionadas
     */
    public function test_scope_activas_filtra_solucionadas(): void
    {
        // Crear alerta activa
        $alertaActiva = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Alerta activa',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        // Crear alerta solucionada
        $alertaSolucionada = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Alerta solucionada',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => true,
            'fecha_solucion' => now(),
            'motivo_solucion' => 'DOCUMENTO_RENOVADO',
            'visible_para' => 'TODOS',
        ]);

        $alertasActivas = Alerta::activas()->get();

        $this->assertCount(1, $alertasActivas);
        $this->assertEquals($alertaActiva->id_alerta, $alertasActivas->first()->id_alerta);
    }

    /**
     * Test: Scope noLeidasPor filtra por usuario
     */
    public function test_scope_no_leidas_por_usuario(): void
    {
        $alerta1 = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Alerta 1',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        $alerta2 = Alerta::create([
            'tipo_alerta' => 'CONDUCTOR',
            'tipo_vencimiento' => 'PROXIMO_VENCER',
            'mensaje' => 'Alerta 2',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        // Admin marca alerta1 como leída
        $alerta1->marcarLeidaPara($this->adminUser->id_usuario);

        // Admin solo debe ver alerta2 como no leída
        $alertasNoLeidasAdmin = Alerta::noLeidasPor($this->adminUser->id_usuario)->get();
        $this->assertCount(1, $alertasNoLeidasAdmin);
        $this->assertEquals($alerta2->id_alerta, $alertasNoLeidasAdmin->first()->id_alerta);

        // SST debe ver ambas como no leídas
        $alertasNoLeidasSST = Alerta::noLeidasPor($this->sstUser->id_usuario)->get();
        $this->assertCount(2, $alertasNoLeidasSST);
    }

    /**
     * Test: Acceso a vista de alertas como ADMIN
     */
    public function test_admin_puede_ver_alertas(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('alertas.index'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Alertas');
    }

    /**
     * Test: Acceso a vista de alertas como PORTERIA
     */
    public function test_porteria_puede_ver_alertas(): void
    {
        $response = $this->actingAs($this->porteriaUser)
            ->get(route('alertas.index'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Alertas');
        $response->assertSee('Ir a Portería'); // Botón especial para PORTERIA
    }

    /**
     * Test: Marcar alerta como leída vía POST
     */
    public function test_marcar_alerta_leida_via_post(): void
    {
        $alerta = Alerta::create([
            'tipo_alerta' => 'VEHICULO',
            'tipo_vencimiento' => 'VENCIDO',
            'mensaje' => 'Test',
            'fecha_alerta' => now()->toDateString(),
            'leida' => false,
            'solucionada' => false,
            'visible_para' => 'TODOS',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('alertas.read', $alerta->id_alerta));

        $response->assertRedirect();

        // Verificar que se guardó en la tabla pivote
        $this->assertDatabaseHas('alerta_usuario_leida', [
            'id_alerta' => $alerta->id_alerta,
            'id_usuario' => $this->adminUser->id_usuario,
        ]);
    }

    /**
     * Test: Marcar todas las alertas como leídas
     */
    public function test_marcar_todas_alertas_leidas(): void
    {
        // Crear varias alertas
        for ($i = 0; $i < 3; $i++) {
            Alerta::create([
                'tipo_alerta' => 'VEHICULO',
                'tipo_vencimiento' => 'VENCIDO',
                'mensaje' => "Alerta $i",
                'fecha_alerta' => now()->toDateString(),
                'leida' => false,
                'solucionada' => false,
                'visible_para' => 'TODOS',
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->post(route('alertas.mark_all_read'));

        $response->assertRedirect();

        // Verificar que todas están marcadas como leídas para este usuario
        $alertasNoLeidas = Alerta::noLeidasPor($this->adminUser->id_usuario)->count();
        $this->assertEquals(0, $alertasNoLeidas);

        // Pero SST aún las ve como no leídas
        $alertasNoLeidasSST = Alerta::noLeidasPor($this->sstUser->id_usuario)->count();
        $this->assertEquals(3, $alertasNoLeidasSST);
    }
}
