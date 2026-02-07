<?php

namespace Tests\Feature;

use App\Models\Conductor;
use App\Models\DocumentoVehiculo;
use App\Models\Propietario;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $adminUser;
    protected Usuario $sstUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = Usuario::factory()->create([
            'rol' => 'ADMIN',
            'activo' => true,
        ]);

        $this->sstUser = Usuario::factory()->create([
            'rol' => 'SST',
            'activo' => true,
        ]);
    }

    // ─── PROPIETARIOS ────────────────────────────────────────────────

    /**
     * Test: Crear propietario vía POST
     */
    public function test_crear_propietario(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('propietarios.store'), [
                'nombre' => 'María',
                'apellido' => 'García',
                'tipo_doc' => 'CC',
                'identificacion' => '1234567890',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('propietarios', [
            'nombre' => 'María',
            'apellido' => 'García',
            'identificacion' => '1234567890',
        ]);
    }

    /**
     * Test: Buscar propietario por identificación
     */
    public function test_buscar_propietario(): void
    {
        Propietario::factory()->create([
            'identificacion' => '9876543210',
            'nombre' => 'Carlos',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('propietarios.buscar', ['identificacion' => '9876543210']));

        $response->assertStatus(200);
    }

    /**
     * Test: Buscar propietario con identificación muy corta
     */
    public function test_buscar_propietario_validacion_minima(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('propietarios.buscar', ['identificacion' => 'ab']));

        $response->assertSessionHasErrors('identificacion');
    }

    // ─── CONDUCTORES ─────────────────────────────────────────────────

    /**
     * Test: Crear conductor con datos válidos
     */
    public function test_crear_conductor(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('conductores.store'), [
                'nombre' => 'Pedro',
                'apellido' => 'Martínez',
                'tipo_doc' => 'CC',
                'identificacion' => '11223344',
                'telefono' => '3001234567',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('conductores', [
            'nombre' => 'Pedro',
            'apellido' => 'Martínez',
            'identificacion' => '11223344',
        ]);
    }

    /**
     * Test: No permite identificación duplicada en conductor
     */
    public function test_conductor_identificacion_unica(): void
    {
        Conductor::factory()->create(['identificacion' => '55667788']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('conductores.store'), [
                'nombre' => 'Juan',
                'apellido' => 'López',
                'tipo_doc' => 'CC',
                'identificacion' => '55667788',
            ]);

        $response->assertSessionHasErrors('identificacion');
    }

    /**
     * Test: Vista de conductores carga correctamente
     */
    public function test_lista_conductores(): void
    {
        Conductor::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('conductores.index'));

        $response->assertStatus(200);
        $response->assertViewHas('conductores');
    }

    /**
     * Test: Vista de crear conductor carga correctamente
     */
    public function test_vista_crear_conductor(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('conductores.create'));

        $response->assertStatus(200);
    }

    // ─── VEHÍCULOS ───────────────────────────────────────────────────

    /**
     * Test: Vista de crear vehículo carga correctamente
     */
    public function test_vista_crear_vehiculo(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('vehiculos.create'));

        $response->assertStatus(200);
    }

    /**
     * Test: SST puede acceder al dashboard
     */
    public function test_sst_puede_ver_dashboard(): void
    {
        $response = $this->actingAs($this->sstUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalVehiculos');
        $response->assertViewHas('conductoresActivos');
    }

    /**
     * Test: ADMIN puede acceder al dashboard
     */
    public function test_admin_puede_ver_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalVehiculos');
    }

    // ─── DOCUMENTOS DE VEHÍCULO ──────────────────────────────────────

    /**
     * Test: Crear documento SOAT para vehículo
     */
    public function test_crear_documento_soat(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.documentos.store', $vehiculo->id_vehiculo), [
                'tipo_documento' => 'SOAT',
                'numero_documento' => 'SOAT-TEST-001',
                'entidad_emisora' => 'Seguros Bolívar',
                'fecha_emision' => Carbon::now()->format('Y-m-d'),
                'nota' => 'Test SOAT',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('documentos_vehiculo', [
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-TEST-001',
        ]);
    }

    /**
     * Test: Crear documento con tipo inválido es rechazado
     */
    public function test_tipo_documento_invalido_rechazado(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.documentos.store', $vehiculo->id_vehiculo), [
                'tipo_documento' => 'DOCUMENTO_FALSO',
                'numero_documento' => 'TEST-001',
                'fecha_emision' => Carbon::now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('tipo_documento');
    }

    /**
     * Test: Historial de documentos de vehículo
     */
    public function test_historial_documentos_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        DocumentoVehiculo::create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'tipo_documento' => 'SOAT',
            'numero_documento' => 'SOAT-H-001',
            'fecha_vencimiento' => Carbon::now()->addMonths(6),
            'activo' => true,
            'version' => 1,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('vehiculos.documentos.historial.completo', $vehiculo->id_vehiculo));

        $response->assertStatus(200);
        $response->assertViewHas('historial');
    }

    // ─── REPORTES ────────────────────────────────────────────────────

    /**
     * Test: Centro de reportes carga correctamente
     */
    public function test_centro_reportes(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('reportes.centro'));

        $response->assertStatus(200);
    }

    /**
     * Test: Reporte de vehículos carga correctamente
     */
    public function test_reporte_vehiculos(): void
    {
        Vehiculo::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('reportes.vehiculos'));

        $response->assertStatus(200);
    }

    // ─── AUTENTICACIÓN ───────────────────────────────────────────────

    /**
     * Test: Login con credenciales válidas
     */
    public function test_login_exitoso(): void
    {
        $user = Usuario::factory()->create([
            'usuario' => 'testuser',
            'password' => bcrypt('password123'),
            'activo' => true,
        ]);

        $response = $this->post(route('login.post'), [
            'usuario' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login con credenciales inválidas
     */
    public function test_login_fallido(): void
    {
        $response = $this->post(route('login.post'), [
            'usuario' => 'noexiste',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    /**
     * Test: Login se bloquea después de 5 intentos fallidos
     */
    public function test_login_bloqueado_por_intentos(): void
    {
        // Realizar 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), [
                'usuario' => 'atacante',
                'password' => 'wrong' . $i,
            ]);
        }

        // El 6to intento debe ser bloqueado con mensaje de throttle
        $response = $this->post(route('login.post'), [
            'usuario' => 'atacante',
            'password' => 'wrong6',
        ]);

        $response->assertSessionHasErrors('usuario');
        $errors = session('errors')->get('usuario');
        // El mensaje puede estar en español ("intentos") o inglés ("attempts")
        $this->assertTrue(
            str_contains($errors[0], 'intentos') || str_contains($errors[0], 'attempts'),
            "El mensaje de bloqueo no contiene la palabra esperada: {$errors[0]}"
        );
    }

    /**
     * Test: Login muestra advertencia cuando quedan pocos intentos
     */
    public function test_login_muestra_intentos_restantes(): void
    {
        // Realizar 3 intentos fallidos (quedan 2)
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login.post'), [
                'usuario' => 'usuario_warn',
                'password' => 'wrong' . $i,
            ]);
        }

        // El 4to intento debe mostrar advertencia de intentos restantes
        $response = $this->post(route('login.post'), [
            'usuario' => 'usuario_warn',
            'password' => 'wrong4',
        ]);

        $response->assertSessionHasErrors('usuario');
        $errors = session('errors')->get('usuario');
        $this->assertStringContainsString('intento(s)', $errors[0]);
    }

    /**
     * Test: Logout funciona correctamente
     */
    public function test_logout(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('logout'));

        $response->assertRedirect();
        $this->assertGuest();
    }
}
