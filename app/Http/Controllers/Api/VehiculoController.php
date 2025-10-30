<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        return response()->json(Vehiculo::with('propietario', 'conductor', 'documentosVehiculo')->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'tipo' => 'required|in:Carro,Moto,Camion,Otro',
            'id_propietario' => 'required|integer|exists:propietarios,id_propietario',
            'id_conductor' => 'nullable|integer|exists:conductores,id_conductor',
            'estado' => 'nullable|in:Activo,Inactivo',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',
        ]);

        $veh = Vehiculo::create($data + ['estado' => $data['estado'] ?? 'Activo']);
        return response()->json($veh, 201);
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load('propietario', 'conductor', 'documentosVehiculo');
        return response()->json($vehiculo);
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $data = $request->validate([
            'placa' => "sometimes|required|string|max:10|unique:vehiculos,placa,{$vehiculo->id_vehiculo},id_vehiculo",
            'marca' => 'sometimes|required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'tipo' => 'sometimes|required|in:Carro,Moto,Camion,Otro',
            'id_propietario' => 'sometimes|required|integer|exists:propietarios,id_propietario',
            'id_conductor' => 'nullable|integer|exists:conductores,id_conductor',
            'estado' => 'nullable|in:Activo,Inactivo',
        ]);

        $vehiculo->update($data);
        return response()->json($vehiculo);
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();
        return response()->json(null, 204);
    }
}
