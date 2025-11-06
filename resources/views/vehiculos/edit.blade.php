@extends('layouts.app')

@section('title','Actualizar Documentos del Vehículo')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Actualizar Documentos — {{ $vehiculo->placa }}</h3>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">← Volver</a>
    </div>

    {{-- Progreso del registro --}}
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar bg-success" style="width: 70%;"></div>
    </div>

    <div class="row g-4">

        {{-- Información del vehículo (solo lectura) --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3 text-secondary"><i class="bi bi-car-front"></i> Información del Vehículo</h5>
                    <div class="row">
                        <div class="col-md-3"><strong>Placa:</strong> {{ $vehiculo->placa }}</div>
                        <div class="col-md-3"><strong>Marca:</strong> {{ $vehiculo->marca }}</div>
                        <div class="col-md-3"><strong>Tipo:</strong> {{ $vehiculo->tipo }}</div>
                        <div class="col-md-3"><strong>Estado:</strong> {{ $vehiculo->estado }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documentos asociados --}}
        @foreach ($vehiculo->documentos as $doc)
        <div class="col-md-6">
            <div class="card shadow-sm border-{{ $doc->estado == 'VENCIDO' ? 'danger' : ($doc->estado == 'POR_VENCER' ? 'warning' : 'success') }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $doc->tipo_documento }}</strong>
                    <span class="badge bg-{{ $doc->estado == 'VENCIDO' ? 'danger' : ($doc->estado == 'POR_VENCER' ? 'warning' : 'success') }}">
                        {{ $doc->estado }}
                    </span>
                </div>

                <div class="card-body">
                    <form action="{{ route('documentos_vehiculo.update', $doc->id_doc_vehiculo) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-2">
                            <label class="form-label">Número de Documento</label>
                            <input type="text" name="numero_documento" value="{{ $doc->numero_documento }}" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Entidad Emisora</label>
                            <input type="text" name="entidad_emisora" value="{{ $doc->entidad_emisora }}" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de Emisión</label>
                                <input type="date" name="fecha_emision" value="{{ $doc->fecha_emision }}" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" value="{{ $doc->fecha_vencimiento }}" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">Actualizar Documento</button>
                            @if($doc->estado == 'VENCIDO')
                            <span class="text-danger small">Documento vencido, debe reemplazarse.</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection