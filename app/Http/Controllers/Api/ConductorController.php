<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conductor;

class ConductorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return response()->json(Conductor::with('documentosConductor', 'vehiculos')->paginate(15));
        //
    }


    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => 'required|in:CC,CE',
            'identificacion' => 'required|string|max:50|unique:conductores,identificacion',
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',
        ]);

        $con = Conductor::create($data + ['activo' => $data['activo'] ?? 1]);
        return response()->json($con, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Conductor $conductor)
    {
        //
        $conductor->load('documentosConductor', 'vehiculos');
        return response()->json($conductor);
    }




    /**
     * Actualizar conductor existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conductor  $conductor
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conductor $conductor)
    {
        $data = $request->validate([

            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'tipo_doc' => 'sometimes|required|in:CC,CE',
            'identificacion' => "sometimes|required|string|max:50|unique:conductores,identificacion,{$conductor->id_conductor},id_conductor",
            'telefono' => 'nullable|string|max:30',
            'telefono_emergencia' => 'nullable|string|max:30',
            'activo' => 'nullable|boolean',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',

        ]);

        $conductor->update($data);
        return response()->json($conductor);
    }


    public function destroy(Conductor $conductor)
    {
        //
        $conductor->delete();
        return response()->json(null, 204);
    }
}
