<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehiculos = Vehiculo::orderBy('id_vehiculo', 'desc')->paginate(12);
        return view('vehiculos.index', compact('vehiculos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiculos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos,placa',
            'marca' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'tipo' => 'required|in:Carro,Moto,Camion,Otro',
            'id_propietario' => 'required|integer|exists:propietarios,id_propietario',
            'id_conductor' => 'nullable|integer|exists:conductores,id_conductor',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Vehiculo::create($validated);
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        return view('vehiculos.edit', compact('vehiculo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $id . ',id_vehiculo',
            'marca' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'tipo' => 'required|in:Carro,Moto,Camion,Otro',
            'id_propietario' => 'required|integer|exists:propietarios,id_propietario',
            'id_conductor' => 'nullable|integer|exists:conductores,id_conductor',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $vehiculo->update($validated);
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente.');
    }
}
