<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentoVehiculo;

class DocumentoVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(DocumentoVehiculo::with('vehiculo')->paginate(20));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([

            'id_vehiculo' => 'required|integer|exists:vehiculos,id_vehiculo',
            'tipo_documento' => 'required|in:SOAT,Tecnomecanica,Tarjeta Propiedad,Póliza,Otro',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora' => 'nullable|string|max:100',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'activo' => 'nullable|boolean',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',

        ]);

        $doc = DocumentoVehiculo::create($data + ['activo' => $data['activo'] ?? 1]);
        return response()->json($doc, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentoVehiculo $documentoVehiculo)
    {
        //
        $documentoVehiculo->load('vehiculo');
        return response()->json($documentoVehiculo);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentoVehiculo $documentoVehiculo)
    {
        $data = $request->validate([
            'tipo_documento' => 'sometimes|required|in:SOAT,Tecnomecanica,Tarjeta Propiedad,Póliza,Otro',
            'numero_documento' => "sometimes|required|string|max:50",
            'entidad_emisora' => 'nullable|string|max:100',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'activo' => 'nullable|boolean',
        ]);
        $documentoVehiculo->update($data);
        return response()->json($$documentoVehiculo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentoVehiculo $documentoVehiculo)
    {
        $documentoVehiculo->delete();
        return response()->json(null, 204);
    }
}
