<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Propietario;
use App\Models\DocumentoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with('propietario')->orderBy('id_vehiculo', 'desc')->paginate(15);
        return view('vehiculos.index', compact('vehiculos'));
    }

    /**
     * Mostrar la vista de create.
     * Si viene ?propietario=ID, cargamos ese propietario para habilitar el formulario de vehículo.
     */
    public function create(Request $request)
    {
        $propietario = null;
        $propId = $request->query('propietario') ?? session('created_propietario_id');

        if ($propId) {
            $propietario = Propietario::find($propId);
        }

        return view('vehiculos.create', compact('propietario'));
    }

    /**
     * Guardar vehículo (requiere id_propietario)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'tipo' => 'required|in:Carro,Moto,Camion,Otro',
            'anio' => 'required|integer|min:1900|max:2099',
            'id_propietario' => 'required|integer|exists:propietarios,id_propietario',
        ], [
            'id_propietario.required' => 'Debe existir un propietario asociado. Crea primero el propietario.',
            'id_propietario.exists' => 'Propietario no válido.',
        ]);

        DB::beginTransaction();
        try {
            $veh = Vehiculo::create([
                'placa' => strtoupper($validated['placa']),
                'marca' => $validated['marca'],
                'modelo' => $validated['modelo'] ?? null,
                'color' => $validated['color'] ?? null,
                'tipo' => $validated['tipo'],
                'anio' => $validated['anio'],
                'id_propietario' => $validated['id_propietario'],
                'id_conductor' => null,
                'estado' => 'Activo',
                'creado_por' => auth()->id() ?? null,
            ]);

            DB::commit();

            return redirect()->route('vehiculos.create', ['propietario' => $veh->id_propietario, 'vehiculo' => $veh->id_vehiculo])->with('success', 'Vehículo creado. Ahora puede agregar los documentos.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creando vehículo: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Error al crear vehículo.']);
        }
    }
}
