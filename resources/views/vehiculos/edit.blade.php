@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('vehiculos.index') }}" style="color:#5B8238;">Vehículos</a>
            </li>
            <li class="breadcrumb-item active">Editar {{ $vehiculo->placa }}</li>
        </ol>
    </nav>

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">
                <i class="fa-solid fa-pen-to-square me-2" style="color:#5B8238;"></i>
                Editar Vehículo
            </h3>
            <p class="text-muted mb-0">
                Placa: <strong>{{ $vehiculo->placa }}</strong>
            </p>
        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6><i class="fa-solid fa-exclamation-circle me-2"></i>Errores de validación:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('vehiculos.update', $vehiculo->id_vehiculo) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-lg-6">

                {{-- DATOS DEL VEHÍCULO --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header text-white" style="background-color:#5B8238;">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-car me-2"></i>
                            Datos del Vehículo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="placa" class="form-label fw-semibold">
                                    Placa <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('placa') is-invalid @enderror"
                                    id="placa"
                                    name="placa"
                                    value="{{ old('placa', $vehiculo->placa) }}"
                                    required>
                                @error('placa')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="marca" class="form-label fw-semibold">
                                    Marca <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('marca') is-invalid @enderror"
                                    id="marca"
                                    name="marca"
                                    value="{{ old('marca', $vehiculo->marca) }}"
                                    required>
                                @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modelo" class="form-label fw-semibold">
                                    Modelo <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('modelo') is-invalid @enderror"
                                    id="modelo"
                                    name="modelo"
                                    value="{{ old('modelo', $vehiculo->modelo) }}"
                                    required>
                                @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label fw-semibold">
                                    Color <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('color') is-invalid @enderror"
                                    id="color"
                                    name="color"
                                    value="{{ old('color', $vehiculo->color) }}"
                                    required>
                                @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label fw-semibold">
                                    Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo') is-invalid @enderror"
                                    id="tipo"
                                    name="tipo"
                                    required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Carro" {{ old('tipo', $vehiculo->tipo) == 'Carro' ? 'selected' : '' }}>Automóvil</option>
                                    <option value="Camion" {{ old('tipo', $vehiculo->tipo) == 'Camion' ? 'selected' : '' }}>Camioneta</option>
                                    <option value="Moto" {{ old('tipo', $vehiculo->tipo) == 'Moto' ? 'selected' : '' }}>Motocicleta</option>
                                    <option value="Otro" {{ old('tipo', $vehiculo->tipo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label fw-semibold">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                    id="estado"
                                    name="estado"
                                    required>
                                    <option value="ACTIVO" {{ old('estado', $vehiculo->estado) == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                    <option value="INACTIVO" {{ old('estado', $vehiculo->estado) == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_matricula" class="form-label fw-semibold">
                                    Fecha de Matrícula
                                    <i class="fa-solid fa-lock text-muted ms-1" title="Campo no editable"></i>
                                </label>
                                <input type="date"
                                    class="form-control"
                                    id="fecha_matricula"
                                    value="{{ $vehiculo->fecha_matricula ? \Carbon\Carbon::parse($vehiculo->fecha_matricula)->format('Y-m-d') : '' }}"
                                    readonly
                                    style="background-color: #e9ecef; cursor: not-allowed;">
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Este campo no es editable por el sistema
                                </small>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="id_conductor" class="form-label fw-semibold">
                                Conductor Asignado
                            </label>
                            <select class="form-select @error('id_conductor') is-invalid @enderror"
                                id="id_conductor"
                                name="id_conductor">
                                <option value="">Sin conductor asignado</option>
                                @foreach($conductores as $conductor)
                                <option value="{{ $conductor->id_conductor }}"
                                    {{ old('id_conductor', $vehiculo->id_conductor) == $conductor->id_conductor ? 'selected' : '' }}>
                                    {{ $conductor->nombre }} {{ $conductor->apellido }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_conductor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-lg-6">

                {{-- DATOS DEL PROPIETARIO --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header text-white" style="background-color:#5B8238;">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-user me-2"></i>
                            Datos del Propietario
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="propietario_nombre" class="form-label fw-semibold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('propietario_nombre') is-invalid @enderror"
                                    id="propietario_nombre"
                                    name="propietario_nombre"
                                    value="{{ old('propietario_nombre', $vehiculo->propietario->nombre) }}"
                                    required>
                                @error('propietario_nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="propietario_apellido" class="form-label fw-semibold">
                                    Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('propietario_apellido') is-invalid @enderror"
                                    id="propietario_apellido"
                                    name="propietario_apellido"
                                    value="{{ old('propietario_apellido', $vehiculo->propietario->apellido) }}"
                                    required>
                                @error('propietario_apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="propietario_documento" class="form-label fw-semibold">
                                Documento Identidad<span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('propietario_documento') is-invalid @enderror"
                                id="propietario_documento"
                                name="propietario_documento"
                                value="{{ old('propietario_documento', $vehiculo->propietario->documento) }}"
                                required>
                            @error('propietario_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>

                {{-- TARJETA DE PROPIEDAD --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-id-card me-2"></i>
                            Tarjeta de Propiedad
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info py-2">
                            <small>
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Documento permanente - No requiere renovación
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="tarjeta_numero" class="form-label fw-semibold">
                                Número de Tarjeta
                            </label>
                            <input type="text"
                                class="form-control @error('tarjeta_numero') is-invalid @enderror"
                                id="tarjeta_numero"
                                name="tarjeta_numero"
                                value="{{ old('tarjeta_numero', $tarjetaPropiedad->numero_documento ?? '') }}">
                            @error('tarjeta_numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tarjeta_entidad" class="form-label fw-semibold">
                                    Entidad Emisora
                                </label>
                                <input type="text"
                                    class="form-control @error('tarjeta_entidad') is-invalid @enderror"
                                    id="tarjeta_entidad"
                                    name="tarjeta_entidad"
                                    value="{{ old('tarjeta_entidad', $tarjetaPropiedad->entidad_emisora ?? '') }}">
                                @error('tarjeta_entidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-0">
                                <label for="tarjeta_fecha_emision" class="form-label fw-semibold">
                                    Fecha de Emisión
                                </label>
                                <input type="date"
                                    class="form-control @error('tarjeta_fecha_emision') is-invalid @enderror"
                                    id="tarjeta_fecha_emision"
                                    name="tarjeta_fecha_emision"
                                    value="{{ old('tarjeta_fecha_emision', $tarjetaPropiedad->fecha_emision ?? '') }}">
                                @error('tarjeta_fecha_emision')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- BOTONES DE ACCIÓN --}}
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between gap-3">
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary px-4">
                        <i class="fa-solid fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn px-4 text-white" style="background-color:#5B8238;">
                        <i class="fa-solid fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

    </form>

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>

</div>

@endsection