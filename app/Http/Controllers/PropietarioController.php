<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropietarioController extends Controller
{
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
