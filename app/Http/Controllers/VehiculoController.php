<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\Conductor;
use App\Models\DocumentoVehiculo;
use App\Traits\SanitizesSearchInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreVehiculoRequest;

class VehiculoController extends Controller
{
    use SanitizesSearchInput;
    /**
     Mostrar una lista de los recursos.
     */
    public function index(Request $request)
    {
        $query = Vehiculo::with(['propietario', 'conductor', 'documentosVehiculo']);

        // Búsqueda (sanitizada contra caracteres especiales LIKE)
        if ($request->filled('search')) {
            $search = $this->prepareLikeSearch($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'like', $search)
                    ->orWhere('marca', 'like', $search)
                    ->orWhere('modelo', 'like', $search)
                    ->orWhereHas('propietario', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', $search)
                            ->orWhere('apellido', 'like', $search);
                    });
            });
        }

        $vehiculos = $query->paginate(15);

        // Contar vehículos eliminados para mostrar badge
        $eliminadosCount = Vehiculo::onlyTrashed()->count();

        return view('vehiculos.index', compact('vehiculos', 'eliminadosCount'));
    }

    /**
     * Mostrar vehículos eliminados (papelera)
     */
    public function trashed(Request $request)
    {
        $query = Vehiculo::onlyTrashed()->with(['propietario']);

        // Búsqueda (sanitizada)
        if ($request->filled('search')) {
            $search = $this->prepareLikeSearch($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'like', $search)
                    ->orWhere('marca', 'like', $search)
                    ->orWhere('modelo', 'like', $search);
            });
        }

        $vehiculos = $query->orderBy('deleted_at', 'desc')->paginate(15);

        return view('vehiculos.trashed', compact('vehiculos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create(Request $request)
    {
        // Obtener propietario si existe en la sesión o URL
        $propietarioId = session('propietario_id') ?? $request->query('propietario');
        $propietario = $propietarioId ? Propietario::find($propietarioId) : null;

        // Obtener vehículo si existe en URL
        $vehiculoId = $request->query('vehiculo');
        $vehiculo = $vehiculoId ? Vehiculo::with('propietario')->find($vehiculoId) : null;

        // Buscar propietario existente por identificación (búsqueda PHP puro)
        $propietarioBuscado = null;
        $identificacionBuscada = $request->query('buscar_identificacion');
        if ($identificacionBuscada && strlen($identificacionBuscada) >= 5 && !$propietario) {
            $propietarioBuscado = Propietario::where('identificacion', $identificacionBuscada)->first();
        }

        return view('vehiculos.create', compact('propietario', 'vehiculo', 'propietarioBuscado', 'identificacionBuscada'));
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     */
    public function store(StoreVehiculoRequest $request)
    {
        $validated = $request->validated();

        try {
            // Crear el vehículo
            $vehiculo = Vehiculo::create([
                'placa'          => strtoupper($validated['placa']),
                'marca'          => $validated['marca'],
                'modelo'         => $validated['modelo'],
                'color'          => $validated['color'],
                'tipo'           => $validated['tipo'],
                'id_propietario' => $validated['id_propietario'],
                'estado'         => 'Activo',
                'creado_por'     => auth()->id() ?? null,
                'fecha_registro' => now(),
            ]);

            // Redirigir con el ID del vehículo para continuar con documentos
            return redirect()
                ->route('vehiculos.create', ['vehiculo' => $vehiculo->id_vehiculo])
                ->with('success', '¡Vehículo creado correctamente! Ahora puedes agregar los documentos.');
        } catch (\Exception $e) {
            Log::error('Error al crear vehículo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al crear el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el formulario para editar el recurso especificado.
     */
    public function edit($id)
    {
        $vehiculo = Vehiculo::with(['propietario', 'documentosVehiculo'])->findOrFail($id);

        // Obtener Tarjeta de Propiedad si existe
        $tarjetaPropiedad = $vehiculo->documentosVehiculo()
            ->where('tipo_documento', 'Tarjeta Propiedad')
            ->where('activo', 1)
            ->first();

        $conductores = Conductor::all();

        return view('vehiculos.edit', compact('vehiculo', 'tarjetaPropiedad', 'conductores'));
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Datos del vehículo
            'placa'           => 'required|string|max:10|unique:vehiculos,placa,' . $id . ',id_vehiculo',
            'marca'           => 'required|string|max:50',
            'modelo'          => 'required|string|max:50',
            'color'           => 'required|string|max:30',
            'tipo'            => 'required|string|max:50',
            'estado'          => 'required|in:Activo,Inactivo',
            'id_conductor'    => 'nullable|exists:conductores,id_conductor',
            'fecha_matricula' => 'nullable|date',

            // Datos del propietario
            'propietario_nombre'    => 'required|string|max:100',
            'propietario_apellido'  => 'required|string|max:100',
            'propietario_documento' => 'required|string|max:20',
            'propietario_telefono'  => 'nullable|string|max:15',
            'propietario_email'     => 'nullable|email|max:100',

            // Datos de Tarjeta de Propiedad (opcional)
            'tarjeta_numero'        => 'nullable|string|max:50',
            'tarjeta_entidad'       => 'nullable|string|max:100',
            'tarjeta_fecha_emision' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($validated, $id, $request) {
                $vehiculo = Vehiculo::findOrFail($id);

                // 1. ACTUALIZAR PROPIETARIO
                $vehiculo->propietario->update([
                    'nombre'    => $validated['propietario_nombre'],
                    'apellido'  => $validated['propietario_apellido'],
                    'documento' => $validated['propietario_documento'],
                    'telefono'  => $validated['propietario_telefono'] ?? null,
                    'email'     => $validated['propietario_email'] ?? null,
                ]);

                // 2. ACTUALIZAR VEHÍCULO
                $vehiculo->update([
                    'placa'           => $validated['placa'],
                    'marca'           => $validated['marca'],
                    'modelo'          => $validated['modelo'],
                    'color'           => $validated['color'],
                    'tipo'            => $validated['tipo'],
                    'estado'          => $validated['estado'],
                    'id_conductor'    => $validated['id_conductor'],
                    'fecha_matricula' => $validated['fecha_matricula'] ?? null,
                ]);

                // 3. ACTUALIZAR O CREAR TARJETA DE PROPIEDAD
                if ($request->filled('tarjeta_numero')) {
                    $tarjetaExistente = DocumentoVehiculo::where('id_vehiculo', $vehiculo->id_vehiculo)
                        ->where('tipo_documento', 'Tarjeta Propiedad')
                        ->where('activo', 1)
                        ->first();

                    if ($tarjetaExistente) {
                        // Actualizar tarjeta existente
                        $tarjetaExistente->update([
                            'numero_documento' => $validated['tarjeta_numero'],
                            'entidad_emisora'  => $validated['tarjeta_entidad'] ?? null,
                            'fecha_emision'    => $validated['tarjeta_fecha_emision'] ?? null,
                        ]);
                    } else {
                        // Crear nueva tarjeta
                        DocumentoVehiculo::create([
                            'id_vehiculo'       => $vehiculo->id_vehiculo,
                            'tipo_documento'    => 'Tarjeta Propiedad',
                            'numero_documento'  => $validated['tarjeta_numero'],
                            'entidad_emisora'   => $validated['tarjeta_entidad'] ?? null,
                            'fecha_emision'     => $validated['tarjeta_fecha_emision'] ?? null,
                            'fecha_vencimiento' => null, // No tiene vencimiento
                            'estado'            => 'VIGENTE',
                            'activo'            => 1,
                            'version'           => 1,
                            'creado_por'        => auth()->id() ?? null,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('vehiculos.index')
                ->with('success', '¡Vehículo actualizado correctamente!');
        } catch (\Exception $e) {
            Log::error('Error al actualizar vehículo', [
                'vehiculo_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete - Ocultar vehículo
     */
    public function destroy($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);

            // Soft delete - no elimina físicamente
            $vehiculo->delete();

            return redirect()
                ->route('vehiculos.index')
                ->with('success', 'Vehículo ocultado correctamente. Se eliminará permanentemente después de 6 meses.');
        } catch (\Exception $e) {
            Log::error('Error al ocultar vehículo', [
                'vehiculo_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Error al ocultar el vehículo.');
        }
    }

    /**
     * Restaurar vehículo eliminado (opcional - para admin)
     */
    public function restore($id)
    {
        try {
            $vehiculo = Vehiculo::onlyTrashed()->findOrFail($id);
            $vehiculo->restore();

            return redirect()
                ->route('vehiculos.index')
                ->with('success', 'Vehículo restaurado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar vehículo', [
                'vehiculo_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Error al restaurar el vehículo.');
        }
    }

    /**
     * Forzar eliminación permanente (opcional - para admin)
     */
    public function forceDelete($id)
    {
        try {
            $vehiculo = Vehiculo::onlyTrashed()->findOrFail($id);
            $vehiculo->forceDelete();

            return redirect()
                ->route('vehiculos.index')
                ->with('success', 'Vehículo eliminado permanentemente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar permanentemente vehículo', [
                'vehiculo_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Error al eliminar el vehículo.');
        }
    }
}
