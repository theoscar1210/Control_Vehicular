<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropietarioController extends Controller
{
    /**
     * Buscar propietario por identificación (AJAX)
     * Retorna los datos del propietario si existe
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'identificacion' => 'required|string|min:3|max:50',
        ]);

        $identificacion = $request->input('identificacion');

        $propietario = Propietario::where('identificacion', $identificacion)->first();

        if ($propietario) {
            return response()->json([
                'encontrado' => true,
                'propietario' => [
                    'id_propietario' => $propietario->id_propietario,
                    'nombre' => $propietario->nombre,
                    'apellido' => $propietario->apellido,
                    'tipo_doc' => $propietario->tipo_doc,
                    'identificacion' => $propietario->identificacion,
                ],
                'vehiculos_count' => $propietario->vehiculos()->count(),
            ]);
        }

        return response()->json(['encontrado' => false]);
    }

    /**
     * Usar propietario existente (redirige a crear vehículo)
     */
    public function usarExistente(Request $request)
    {
        $request->validate([
            'id_propietario' => 'required|exists:propietarios,id_propietario',
        ]);

        return redirect()
            ->route('vehiculos.create', ['propietario' => $request->id_propietario])
            ->with('success', 'Propietario seleccionado. Ahora puede registrar el vehículo.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => 'required|in:CC,NIT',
            'identificacion' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        // Si el propietario ya existe (registro previo interrumpido), continuar directamente al paso 2
        $existente = Propietario::where('identificacion', $data['identificacion'])->first();
        if ($existente) {
            $tieneVehiculos = $existente->vehiculos()->count() > 0;
            $mensaje = $tieneVehiculos
                ? "El propietario {$existente->nombre} {$existente->apellido} ya existe en el sistema. Selecciona el vehículo que deseas gestionar."
                : "El propietario {$existente->nombre} {$existente->apellido} ya estaba registrado (posiblemente de un registro interrumpido). Continúa agregando el vehículo.";

            return redirect()
                ->route('vehiculos.create', ['propietario' => $existente->id_propietario])
                ->with('info', $mensaje);
        }

        DB::beginTransaction();
        try {
            $prop = Propietario::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'tipo_doc' => $data['tipo_doc'],
                'identificacion' => $data['identificacion'],
                'creado_por' => auth()->id() ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('vehiculos.create', ['propietario' => $prop->id_propietario])
                ->with('success', 'Propietario creado correctamente. Ahora puede registrar el vehículo.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creando propietario: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Error al crear propietario. Intenta de nuevo.']);
        }
    }
}
