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
            'identificacion' => 'required|string|max:50|unique:propietarios,identificacion',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

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

            // Redirigir a la página de creación de vehículo con ?propietario=ID
            return redirect()
                ->route('vehiculos.create', ['propietario' => $prop->id_propietario])
                ->with('success', 'Propietario creado correctamente. Ahora puede registrar el vehículo.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error creando propietario: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Error al crear propietario.']);
        }
    }
}
