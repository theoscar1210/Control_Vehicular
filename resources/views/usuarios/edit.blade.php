@extends('layouts.app')

@section('title','Editar Usuario')

@section('content')
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
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button class="btn btn-primary">Actualizar usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection