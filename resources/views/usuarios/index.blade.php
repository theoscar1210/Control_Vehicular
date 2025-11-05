@extends('layouts.app')

@section('title','Gestión de Usuarios')

@section('content')
<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-seccion mb-0">
            <i class="fas fa-users me-2 text-success"></i>Gestión de Usuarios
        </h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-principal btn-sm">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Tarjeta contenedora --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            {{-- Tabla moderna --}}
            <table class="table table-hover align-middle mb-0">
                <thead class="table-usuarios">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Activo</th>
                        <th class="text-center" style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-usuarios-body">
                    @forelse($usuarios as $u)
                    <tr>
                        <td>{{ $u->id_usuario }}</td>
                        <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                        <td>{{ $u->usuario }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ $u->rol }}</span></td>
                        <td>
                            @if($u->activo)
                            <span class="badge bg-success">Sí</span>
                            @else
                            <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('usuarios.edit', $u->id_usuario) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar usuario?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="card-footer bg-white border-0">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection