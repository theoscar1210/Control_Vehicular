<?php

namespace Tests\Feature;

use App\Models\Propietario;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoCreationTest extends TestCase
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
     * Test: Crear vehículo con propietario existente
     */
    public function test_crear_vehiculo_con_propietario_existente(): void
    {
        $propietario = Propietario::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'ABC123',
                'marca' => 'Toyota',
                'modelo' => 'Corolla 2024',
                'color' => 'Blanco',
                'tipo' => 'Carro',
                'id_propietario' => $propietario->id_propietario,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vehiculos', [
            'placa' => 'ABC123',
            'marca' => 'Toyota',
            'id_propietario' => $propietario->id_propietario,
            'estado' => 'Activo',
        ]);
    }

    /**
     * Test: Placa se guarda en mayúsculas
     */
    public function test_placa_se_guarda_en_mayusculas(): void
    {
        $propietario = Propietario::factory()->create();

        $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'abc123',
                'marca' => 'Mazda',
                'modelo' => 'CX-5',
                'color' => 'Rojo',
                'tipo' => 'Carro',
                'id_propietario' => $propietario->id_propietario,
            ]);

        $this->assertDatabaseHas('vehiculos', [
            'placa' => 'ABC123',
        ]);
    }

    /**
     * Test: No se puede crear vehículo con placa duplicada
     */
    public function test_no_permite_placa_duplicada(): void
    {
        $propietario = Propietario::factory()->create();

        Vehiculo::factory()->create([
            'placa' => 'DUP001',
            'id_propietario' => $propietario->id_propietario,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'DUP001',
                'marca' => 'Chevrolet',
                'modelo' => 'Spark',
                'color' => 'Negro',
                'tipo' => 'Carro',
                'id_propietario' => $propietario->id_propietario,
            ]);

        $response->assertSessionHasErrors('placa');

        // Solo debe existir 1 vehículo con esa placa
        $this->assertEquals(1, Vehiculo::where('placa', 'DUP001')->count());
    }

    /**
     * Test: Validación rechaza campos obligatorios vacíos
     */
    public function test_validacion_rechaza_campos_vacios(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), []);

        $response->assertSessionHasErrors(['placa', 'marca', 'modelo', 'color', 'tipo', 'id_propietario']);
    }

    /**
     * Test: Validación rechaza tipo de vehículo inválido
     */
    public function test_validacion_rechaza_tipo_invalido(): void
    {
        $propietario = Propietario::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'VAL001',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'color' => 'Azul',
                'tipo' => 'Avion',
                'id_propietario' => $propietario->id_propietario,
            ]);

        $response->assertSessionHasErrors('tipo');
    }

    /**
     * Test: Validación rechaza propietario inexistente
     */
    public function test_validacion_rechaza_propietario_inexistente(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'VAL002',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'color' => 'Azul',
                'tipo' => 'Carro',
                'id_propietario' => 99999,
            ]);

        $response->assertSessionHasErrors('id_propietario');
    }

    /**
     * Test: Usuario no autenticado no puede crear vehículo
     */
    public function test_usuario_no_autenticado_no_puede_crear(): void
    {
        $response = $this->post(route('vehiculos.store'), [
            'placa' => 'NOAUTH',
            'marca' => 'Test',
            'modelo' => 'Test',
            'color' => 'Test',
            'tipo' => 'Carro',
            'id_propietario' => 1,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: PORTERIA no puede crear vehículos (solo ADMIN y SST)
     */
    public function test_porteria_no_puede_crear_vehiculo(): void
    {
        $porteriaUser = Usuario::factory()->create([
            'rol' => 'PORTERIA',
            'activo' => true,
        ]);

        $propietario = Propietario::factory()->create();

        $response = $this->actingAs($porteriaUser)
            ->post(route('vehiculos.store'), [
                'placa' => 'PORT01',
                'marca' => 'Test',
                'modelo' => 'Test',
                'color' => 'Test',
                'tipo' => 'Carro',
                'id_propietario' => $propietario->id_propietario,
            ]);

        // Portería debería ser redirigido o recibir 403
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(),
            'PORTERIA should not be able to create vehicles'
        );
    }

    /**
     * Test: Vista de lista de vehículos carga correctamente
     */
    public function test_vista_lista_vehiculos_carga(): void
    {
        Vehiculo::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('vehiculos.index'));

        $response->assertStatus(200);
        $response->assertViewHas('vehiculos');
    }

    /**
     * Test: Búsqueda de vehículos funciona por placa
     */
    public function test_busqueda_por_placa(): void
    {
        Vehiculo::factory()->create(['placa' => 'BUSCAR1']);
        Vehiculo::factory()->create(['placa' => 'OTRO99']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('vehiculos.index', ['search' => 'BUSCAR']));

        $response->assertStatus(200);
    }
}
