<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    /**
     * Mostrar lista de usuarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Usuario::paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'rol' => 'required|in:ADMIN,SST,PORTERIA',
            'activo' => 'nullable|boolean',
        ]);

        $data['password'] = bcrypt($data['password']);
        $user = Usuario::create($data + ['activo' => $data['activo'] ?? 1]);

        return response()->json($user, 201);
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'usuario' => "sometimes|required|string|max:50|unique:usuarios,usuario,{$usuario->id_usuario},id_usuario",
            'email' => "sometimes|required|email|max:255|unique:usuarios,email,{$usuario->id_usuario},id_usuario",
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()],
            'rol' => 'nullable|in:ADMIN,SST,PORTERIA',
            'activo' => 'nullable|boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);
        return response()->json($usuario);
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(null, 204);
    }
}
