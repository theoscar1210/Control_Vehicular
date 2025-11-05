@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<br><br>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Nuevo Usuario</h3>
        <a href="{{ route('usuarios.index') }}" class="btn btn-principal btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('usuarios.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Apellido</label>
                        <input name="apellido" value="{{ old('apellido') }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Usuario</label>
                        <input name="usuario" value="{{ old('usuario') }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Contraseña</label>
                        <input name="password" type="password" class="form-control rounded-3 border-success-subtle" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Confirmar contraseña</label>
                        <input name="password_confirmation" type="password" class="form-control rounded-3 border-success-subtle" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Rol</label>
                        <select name="rol" class="form-select rounded-3 border-success-subtle" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="ADMIN" {{ old('rol')=='ADMIN' ? 'selected' : '' }}>ADMIN</option>
                            <option value="SST" {{ old('rol')=='SST' ? 'selected' : '' }}>SST</option>
                            <option value="PORTERIA" {{ old('rol')=='PORTERIA' ? 'selected' : '' }}>PORTERIA</option>
                        </select>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" id="activo" class="form-check-input" checked>
                            <label class="form-check-label fw-semibold" for="activo">Usuario activo</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger sm">
                        Cancelar
                    </a>
                    <button class="btn btn-principal btn-sm">
                        <i class="bi bi-person-plus"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection