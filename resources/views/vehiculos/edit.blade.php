@extends('layouts.app') {{-- Extiende el layout principal de la aplicación --}}

@section('title', 'Actualizar Documentos del Vehículo') {{-- Título de la página --}}

@section('content')
<div class="container py-4"> {{-- Contenedor principal con padding vertical --}}

    {{-- Mensajes de éxito o error después de acciones --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Encabezado de la sección con botón de regreso --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Documentos del Vehículo — {{ $vehiculo->placa }}</h3>
            <small class="text-muted">Actualiza o reemplaza documentos asociados</small>
        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">← Volver</a>
    </div>

    {{-- Tarjeta con información básica del vehículo --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3 text-secondary"><i class="bi bi-car-front"></i> Datos del Vehículo</h5>
            <div class="row">
                {{-- Datos básicos del vehículo --}}
                <div class="col-md-3"><strong>Placa:</strong> {{ $vehiculo->placa }}</div>
                <div class="col-md-3"><strong>Marca:</strong> {{ $vehiculo->marca }}</div>
                <div class="col-md-3"><strong>Tipo:</strong> {{ $vehiculo->tipo }}</div>
                <div class="col-md-3"><strong>Modelo:</strong> {{ $vehiculo->modelo }}</div>
                <div class="col-md-3"><strong>Color:</strong> {{ $vehiculo->color }}</div>
                <div class="col-md-3"><strong>Estado:</strong> {{ $vehiculo->estado }}</div>
                {{-- Datos del propietario --}}
                <div class="col-md-4 mt-2"><strong>Propietario:</strong>
                    {{ $vehiculo->propietario->nombre ?? 'N/A' }} {{ $vehiculo->propietario->apellido ?? '' }}
                </div>
                {{-- Datos del conductor --}}
                <div class="col-md-4 mt-2"><strong>Conductor:</strong>
                    {{ $vehiculo->conductor->nombre ?? 'N/A' }} {{ $vehiculo->conductor->apellido ?? '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Sección principal de documentos del vehículo --}}
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3 text-secondary">Documentos del Vehículo</h5>

            {{-- Mensaje si no hay documentos registrados --}}
            @if($vehiculo->documentosVehiculo->isEmpty())
            <div class="alert alert-warning">No hay documentos registrados para este vehículo.</div>
            @endif

            <div class="row">
                {{-- Iteración por cada documento del vehículo --}}
                @foreach($vehiculo->documentosVehiculo as $doc)
                @php
                // Asignación de color según estado del documento
                $color = match($doc->estado) {
                'VIGENTE' => 'success',
                'POR_VENCER' => 'warning',
                'VENCIDO' => 'danger',
                'REEMPLAZADO' => 'secondary',
                default => 'secondary'
                };
                @endphp

                {{-- Tarjeta individual por documento --}}
                <div class="col-md-6 mb-4">
                    <div class="card border-{{ $color }} shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>{{ $doc->tipo_documento }}</strong>
                            <span class="badge bg-{{ $color }}">{{ $doc->estado }}</span>
                        </div>
                        <div class="card-body">
                            {{-- Formulario para actualizar documento --}}
                            <form action="{{ route('documentos_vehiculo.update', $doc->id_doc_vehiculo) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Campo número de documento --}}
                                <div class="mb-2">
                                    <label class="form-label">Número de Documento</label>
                                    <input type="text" name="numero_documento" value="{{ old('numero_documento', $doc->numero_documento) }}" class="form-control">
                                </div>

                                {{-- Campo entidad emisora --}}
                                <div class="mb-2">
                                    <label class="form-label">Entidad Emisora</label>
                                    <input type="text" name="entidad_emisora" value="{{ old('entidad_emisora', $doc->entidad_emisora) }}" class="form-control">
                                </div>

                                {{-- Fechas de emisión y vencimiento --}}
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">F. Emisión</label>
                                        <input type="date" name="fecha_emision" value="{{ old('fecha_emision', optional($doc->fecha_emision)->format('Y-m-d') ?? $doc->fecha_emision) }}" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">F. Vencimiento</label>
                                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', optional($doc->fecha_vencimiento)->format('Y-m-d') ?? $doc->fecha_vencimiento) }}" class="form-control">
                                    </div>
                                </div>

                                {{-- Botón de acción y alerta si está vencido --}}
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                                    @if($doc->estado == 'VENCIDO')
                                    <span class="text-danger small">Documento vencido, reemplazar pronto</span>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Sección de documentos del conductor (si existen) --}}

        @if($vehiculo->conductor && $vehiculo->conductor->documentosConductor->isNotEmpty())

        <div class="col-md-12 mt-5">
            <h5 class="mb-3 text-secondary">Documentos del Conductor</h5>
            <div class="row">
                @foreach($vehiculo->conductor->documentosConductor as $doc)
                @php
                // Asignación de color según estado del documento
                $color = match($doc->estado) {
                'VIGENTE' => 'success',
                'POR_VENCER' => 'warning',
                'VENCIDO' => 'danger',
                'REEMPLAZADO' => 'secondary',
                default => 'secondary'
                };
                @endphp

                {{-- Tarjeta individual por documento del conductor --}}
                <div class="col-md-6 mb-4">
                    <div class="card border-{{ $color }} shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>{{ $doc->tipo_documento }}</strong>
                            <span class="badge bg-{{ $color }}">{{ $doc->estado }}</span>
                        </div>

                        <div class="card-body">

                            {{-- Formulario para actualizar documento del conductor --}}
                            <form action="{{ route('documentos_conductor.update', $doc->id_doc_conductor) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-2">
                                    <label class="form-label">Número</label>
                                    <input type="text" name="numero_documento" value="{{ old('numero_documento', $doc->numero_documento) }}" class="form-control">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">F. Emisión</label>
                                        <input type="date" name="fecha_emision" value="{{ old('fecha_emision', optional($doc->fecha_emision)->format('Y-m-d') ?? $doc->fecha_emision) }}" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">F. Vencimiento</label>
                                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', optional($doc->fecha_vencimiento)->format('Y-m-d') ?? $doc->fecha_vencimiento) }}" class="form-control">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection