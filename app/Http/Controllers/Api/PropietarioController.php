<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Propietario;

class PropietarioController extends Controller
{
    /**
     * Mostrar una lista de los recursos.
     */
    public function index()
    {
        //
        return response()->json(Propietario::with('vehiculos')->paginate(15));
    }

    /* 
    Almacenar un recurso recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'tipo_doc' => 'required|in:CC,NIT',
            'identificacion' => 'required|string|max:50|unique:propietarios,identificacion',
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',
        ]);

        $prop = Propietario::create($data);
        return response()->json($prop, 201);
    }


    public function show(Propietario $propietario)
    {
        //
        $propietario->load('vehiculos');
        return response()->json($propietario);
    }





    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, Propietario $propietario)
    {
        //
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'tipo_doc' => 'sometimes|required|in:CC,NIT',
            'identificacion' => "sometimes|required|string|max:50|unique:propietarios,identificacion,{$propietario->id_propietario},id_propietario",
            'creado_por' => 'nullable|integer|exists:usuarios,id_usuario',

        ]);

        $propietario->update($data);
        return response()->json($propietario);
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Propietario $propietario)
    {
        //
        $propietario->delete();
        return response()->json(null, 204);
    }
}
