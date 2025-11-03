@extends('layouts.app')

@section('title','Gestión de Usuarios')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Gestión de Usuarios</h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo Usuario</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Activo</th>
                        <th style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                    <tr>
                        <td>{{ $u->id_usuario }}</td>
                        <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                        <td>{{ $u->usuario }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ $u->rol }}</span></td>
                        <td>{{ $u->activo ? 'Sí' : 'No' }}</td>
                        <td>
                            <a href="{{ route('usuarios.edit', $u->id_usuario) }}" class="btn btn-sm btn-warning">Editar</a>

                            <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar usuario?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection