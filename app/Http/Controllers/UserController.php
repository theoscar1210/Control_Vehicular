<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Ajusta el campo de orden según tu PK
        $usuarios = User::orderBy('id_usuario', 'desc')->paginate(12);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'usuario'  => 'required|string|max:50|unique:usuarios,usuario',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'rol'      => 'required|in:ADMIN,SST,PORTERIA',
            'activo'   => 'nullable|in:0,1',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        User::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'usuario'  => 'required|string|max:50|unique:usuarios,usuario,' . $id . ',id_usuario',
            'email'    => 'required|email|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => 'nullable|string|min:6|confirmed',
            'rol'      => 'required|in:ADMIN,SST,PORTERIA',
            'activo'   => 'nullable|in:0,1',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $usuario->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
