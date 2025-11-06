@extends('layouts.app')

@section('title','Editar Usuario')
a@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mt-4">
    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-seccion mb-0">
            <i class="fas fa-user-edit me-2 text-success"></i> Editar Usuario
        </h3>
        <a href="{{ route('usuarios.index') }}" class="btn btn-principal btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Errores --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Formulario --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-naranja text-white fw-semibold">
            <i class="fas fa-id-card me-1"></i> Información del Usuario
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input name="apellido" value="{{ old('apellido', $usuario->apellido) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input name="usuario" value="{{ old('usuario', $usuario->usuario) }}" class="form-control rounded-3 border-success-subtle" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email', $usuario->email) }}" class="form-control rounded-3 border-success-subtle" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva contraseña (opcional)</label>
                        <input name="password" type="password" class="form-control rounded-3 border-success-subtle" minlength="6" maxlength="10" title="La contraseña debe tener entre 6 y 10 caracteres">
                        <small class="text-muted">Debe tener entre 6 y 10 caracteres.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input name="password_confirmation" type="password" class="form-control rounded-3 border-success-subtle" minlength="6" maxlength="10" title="Debe coincidir con la contraseña anterior">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="ADMIN" {{ $usuario->rol === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                        <option value="SST" {{ $usuario->rol === 'SST' ? 'selected' : '' }}>SST</option>
                        <option value="PORTERIA" {{ $usuario->rol === 'PORTERIA' ? 'selected' : '' }}>PORTERIA</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="activo" value="0"> {{-- Valor por defecto si el checkbox no se marca --}}
                    <input
                        type="checkbox"
                        name="activo"
                        id="activo"
                        value="1"
                        class="form-check-input"
                        {{ old('activo', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>


                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button class="btn btn-principal btn-sm">
                        <i class="fas fa-save me-1"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('content')
<br><br>
<div class="container">
    <h3>Editar Usuario</h3>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input name="apellido" value="{{ old('apellido', $usuario->apellido) }}" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input name="usuario" value="{{ old('usuario', $usuario->usuario) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email', $usuario->email) }}" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva contraseña (opcional)</label>
                        <input name="password" type="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input name="password_confirmation" type="password" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="ADMIN" {{ $usuario->rol==='ADMIN' ? 'selected' : '' }}>ADMIN</option>
                        <option value="SST" {{ $usuario->rol==='SST' ? 'selected' : '' }}>SST</option>
                        <option value="PORTERIA" {{ $usuario->rol==='PORTERIA' ? 'selected' : '' }}>PORTERIA</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" {{ $usuario->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger">Cancelar</a>
                    <button class="btn btn-principal btn-sm">Actualizar usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection