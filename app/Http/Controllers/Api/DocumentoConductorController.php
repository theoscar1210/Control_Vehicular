<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentoConductor;
use Illuminate\Http\Request;

class DocumentoConductorController extends Controller
{
    public function index()
    {
        return response()->json(DocumentoConductor::with('conductor')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_conductor' => 'required|integer|exists:conductores,id_conductor',
            'tipo_documento' => 'required|in:Licencia Conducción,EPS,ARL,Certificado Médico,Otro',
            'numero_documento' => 'required|string|max:50',
            'entidad_emisora' => 'nullable|string|max:100',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'activo' => 'nullable|boolean',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',
        ]);

        $doc = DocumentoConductor::create($data + ['activo' => $data['activo'] ?? 1]);
        return response()->json($doc, 201);
    }

    public function show(DocumentoConductor $documentoConductor)
    {
        $documentoConductor->load('conductor');
        return response()->json($documentoConductor);
    }

    public function update(Request $request, DocumentoConductor $documentoConductor)
    {
        $data = $request->validate([
            'tipo_documento' => 'sometimes|required|in:Licencia Conducción,EPS,ARL,Certificado Médico,Otro',
            'numero_documento' => "sometimes|required|string|max:50",
            'entidad_emisora' => 'nullable|string|max:100',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|in:VIGENTE,POR_VENCER,VENCIDO,REEMPLAZADO',
            'activo' => 'nullable|boolean',
        ]);

        $documentoConductor->update($data);
        return response()->json($documentoConductor);
    }

    public function destroy(DocumentoConductor $documentoConductor)
    {
        $documentoConductor->delete();
        return response()->json(null, 204);
    }
}
