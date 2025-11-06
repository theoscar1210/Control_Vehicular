@extends('layouts.app')

@section('title', 'Nuevo Vehículo')

@section('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@endsection

@section('content')
<div class="container mt-4">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-4">
            <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}"><i class="fa-solid fa-car"></i> Vehículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Registro</li>
        </ol>
    </nav>

    <h3><i class="fa-solid fa-circle-plus text-primary"></i> Registro de Vehículo</h3>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="alert alert-success"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> Corrige los siguientes errores:
        <ul class="mb-0 mt-2">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row gy-4">

        {{-- FORMULARIO PROPIETARIO --}}
        <div class="col-12 shadow-lg">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-user"></i> 1. Registrar propietario</div>
                <div class="card-body">
                    <form action="{{ route('propietarios.store') }}" method="POST" id="form-propietario">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido *</label>
                                <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo Documento *</label>
                                <select name="tipo_doc" class="form-select" required>
                                    <option value="CC">CC</option>
                                    <option value="NIT">NIT</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Identificación *</label>
                                <input type="text" name="identificacion" class="form-control" value="{{ old('identificacion') }}" required>
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-3">
                            <button class="btn btn-primary"><i class="fa-solid fa-user-plus me-1"></i> Crear propietario</button>
                            <small class="text-muted">Al crear, se habilitará el formulario de vehículo.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2 FORMULARIO VEHÍCULO --}}
        <div class="col-12 shadow-lg">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-car"></i> 2. Registrar vehículo</div>
                <div class="card-body">
                    @if(!$propietario)
                    <div class="alert alert-warning mb-3">
                        <i class="fa-solid fa-lock me-2"></i> Debes crear primero el propietario. Luego podrás registrar el vehículo.
                    </div>
                    @endif

                    <form action="{{ route('vehiculos.store') }}" method="POST">
                        @csrf
                        @if($propietario)
                        <input type="hidden" name="id_propietario" value="{{ $propietario->id_propietario }}">
                        @endif

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label>Placa *</label>
                                <input type="text" name="placa" class="form-control" {{ $propietario ? '' : 'disabled' }}>
                            </div>
                            <div class="col-md-3">
                                <label>Tipo *</label>
                                <select name="tipo" class="form-select" {{ $propietario ? '' : 'disabled' }}>
                                    <option value="">Seleccionar</option>
                                    <option value="Carro">Carro</option>
                                    <option value="Moto">Moto</option>
                                    <option value="Camión">Camión</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Marca *</label>
                                <input type="text" name="marca" class="form-control" {{ $propietario ? '' : 'disabled' }}>
                            </div>
                            <div class="col-md-3">
                                <label>Año *</label>
                                <input type="number" name="anio" min="1900" max="2099" class="form-control" {{ $propietario ? '' : 'disabled' }}>
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-3">
                            <button class="btn btn-success" {{ $propietario ? '' : 'disabled' }}>
                                <i class="fa-solid fa-car-side me-1"></i> Crear vehículo
                            </button>
                            <small class="text-muted">
                                Propietario actual:
                                @if($propietario)
                                <strong>{{ $propietario->nombre }} {{ $propietario->apellido }}</strong>
                                @else
                                <em>(ninguno)</em>
                                @endif
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3 FORMULARIO DOCUMENTOS --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-folder-open"></i> 3. Documentos (SOAT / Tecnomecánica)</div>
                <div class="card-body">
                    @php $vehiculoId = request()->query('vehiculo') ?? null; @endphp

                    @if(!$vehiculoId)
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i> Registra primero el vehículo para agregar documentos.
                    </div>
                    @else
                    <form action="{{ route('documentos.store', $vehiculoId) }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <h5>Registrar Documento del Vehículo</h5>

                        <div class="row g-3 shadow-lg">
                            <div class="col-md-4">
                                <label class="form-label">Tipo de documento</label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="SOAT">SOAT</option>
                                    <option value="Tecnomecánica">Tecnomecánica</option>
                                    <option value="Tarjeta Propiedad">Tarjeta de Propiedad</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Número</label>
                                <input type="text" name="numero_documento" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Entidad emisora</label>
                                <input type="text" name="entidad_emisora" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha emisión</label>
                                <input type="date" name="fecha_emision" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-solid fa-save me-2"></i> Guardar Documento
                            </button>

                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = new URL(window.location.href);
        if (url.searchParams.get('propietario')) {
            document.querySelector('.card:nth-child(2)').scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
</script>
@endsection
@endsection