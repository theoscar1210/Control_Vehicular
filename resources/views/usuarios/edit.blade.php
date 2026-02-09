@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mt-4">
    {{-- Titulo --}}
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

    {{-- Mensaje de exito --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Formulario --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-naranja text-white fw-semibold">
            <i class="fas fa-id-card me-1"></i> Informacion del Usuario
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input name="apellido" value="{{ old('apellido', $usuario->apellido) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Usuario <span class="text-danger">*</span></label>
                        <input name="usuario" value="{{ old('usuario', $usuario->usuario) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input name="email" type="email" value="{{ old('email', $usuario->email) }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva contrasena (opcional)</label>
                        <input name="password" type="password" class="form-control rounded-3 border-success-subtle" minlength="8" placeholder="Dejar vacio para mantener la actual">
                        <small class="text-muted">Min. 8 caracteres, una mayuscula, una minuscula y un numero.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar contrasena</label>
                        <input name="password_confirmation" type="password" class="form-control rounded-3 border-success-subtle" minlength="8">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select name="rol" class="form-select rounded-3 border-success-subtle" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="ADMIN" {{ old('rol', $usuario->rol) === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                            <option value="SST" {{ old('rol', $usuario->rol) === 'SST' ? 'selected' : '' }}>SST</option>
                            <option value="PORTERIA" {{ old('rol', $usuario->rol) === 'PORTERIA' ? 'selected' : '' }}>PORTERIA</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input" {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Usuario Activo</label>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-universal">
                        <i class="fas fa-save me-1"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        &copy; 2025 Club Campestre Altos del Chicala. Todos los derechos reservados.
    </footer>
</div>
@endsection
