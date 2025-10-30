<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        return response()->json(Alerta::with(['documentoVehiculo.vehiculo', 'documentoConductor.conductor'])->orderByDesc('fecha_registro')->paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_alerta' => 'required|in:VEHICULO,CONDUCTOR',
            'id_doc_vehiculo' => 'nullable|integer|exists:documentos_vehiculo,id_doc_vehiculo',
            'id_doc_conductor' => 'nullable|integer|exists:documentos_conductor,id_doc_conductor',
            'tipo_vencimiento' => 'required|in:VENCIDO,PROXIMO_VENCER',
            'mensaje' => 'nullable|string|max:255',
            'fecha_alerta' => 'nullable|date',
            'leida' => 'nullable|boolean',
            'visible_para' => 'nullable|in:ADMIN,SST,PORTERIA,TODOS',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',
        ]);

        $alert = Alerta::create($data + ['fecha_alerta' => $data['fecha_alerta'] ?? now()->format('Y-m-d')]);
        return response()->json($alert, 201);
    }

    public function show(Alerta $alerta)
    {
        $alerta->load(['documentoVehiculo.vehiculo', 'documentoConductor.conductor']);
        return response()->json($alerta);
    }

    public function update(Request $request, Alerta $alerta)
    {
        $data = $request->validate([
            'leida' => 'nullable|boolean',
        ]);

        $alerta->update($data);
        return response()->json($alerta);
    }

    public function destroy(Alerta $alerta)
    {
        $alerta->delete();
        return response()->json(null, 204);
    }
}
