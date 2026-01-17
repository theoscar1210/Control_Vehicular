@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;

// Documentos activos e históricos
$documentosActivos = $historial->where('activo', 1);
$documentosHistoricos = $historial->where('activo', 0);

// Conteo
$vigentes = $documentosActivos->where('estado','VIGENTE')->count();
$porVencer = $documentosActivos->where('estado','POR_VENCER')->count();
$vencidos = $documentosActivos->where('estado','VENCIDO')->count();
@endphp

@extends('layouts.app')
@section('title','Documentos del Vehículo')

@section('content')
<br><br><br>
<div class="container-fluid py-4">

    {{-- ================= ENCABEZADO ================= --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('vehiculos.index') }}" style="color:#5B8238;">Vehículos</a>
            </li>
            <li class="breadcrumb-item active">Documentos {{ $vehiculo->placa }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">
                <i class="fa-solid fa-file-lines me-2"></i>
                Documentos del Vehículo
            </h3>
            <p class="text-muted mb-0">
                <strong>{{ $vehiculo->placa }}</strong> — {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
            </p>
        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- ================= ALERTAS ================= --}}
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
        <h6 class="alert-heading"><i class="fa-solid fa-circle-exclamation me-2"></i>Errores de validación:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ================= RESUMEN ================= --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-success">{{ $vigentes }}</h2>
                    <small class="text-muted">Documentos Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-warning">{{ $porVencer }}</h2>
                    <small class="text-muted">Por Vencer (≤30 días)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-danger">{{ $vencidos }}</h2>
                    <small class="text-muted">Vencidos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold" style="color:#5B8238;">{{ $documentosActivos->count() }}</h2>
                    <small class="text-muted">Total Activos</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= DOCUMENTOS ACTIVOS ================= --}}
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header text-white" style="background-color:#5B8238;">
            <h5 class="mb-0">
                <i class="fa-solid fa-file-circle-check me-2"></i>
                Documentos Activos
            </h5>
        </div>

        <div class="card-body p-4">
            @if($documentosActivos->isEmpty())
            <div class="text-center py-5">
                <i class="fa-solid fa-folder-open text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No hay documentos activos registrados.</p>
            </div>
            @else
            <div class="row">
                @foreach($documentosActivos as $doc)
                {{-- SOAT --}}
                @if($doc->tipo_documento === 'SOAT')
                @include('vehiculos.documentos.partials.card-soat', [
                'doc' => $doc,
                'vehiculo' => $vehiculo
                ])
                @endif

                {{-- TECNOMECÁNICA --}}
                @if($doc->tipo_documento === 'Tecnomecanica')
                @include('vehiculos.documentos.partials.card-tecnomecanica', [
                'doc' => $doc,
                'vehiculo' => $vehiculo
                ])
                @endif

                {{-- OTROS DOCUMENTOS --}}
                @if(!in_array($doc->tipo_documento, ['SOAT', 'Tecnomecanica']))
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border-info" style="border-width: 2px;">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fa-solid fa-file-alt me-1"></i>
                                {{ str_replace('_', ' ', $doc->tipo_documento) }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Número:</strong> {{ $doc->numero_documento }}
                            </p>
                            @if($doc->fecha_emision)
                            <p class="mb-2">
                                <strong>Emisión:</strong> {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                            </p>
                            @endif
                            @if($doc->entidad_emisora)
                            <p class="mb-0">
                                <strong>Entidad:</strong> {{ $doc->entidad_emisora }}
                            </p>
                            @endif
                        </div>
                        <div class="card-footer bg-light text-center">
                            <span class="badge bg-info">
                                <i class="fa-solid fa-info-circle me-1"></i> Documento Permanente
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ================= MODALES DE RENOVACIÓN (SOLO VENCIDOS) ================= --}}
    @foreach($documentosActivos->whereIn('estado', ['VENCIDO', 'POR_VENCER']) as $doc)
    @if($doc->tipo_documento === 'SOAT')
    @include('vehiculos.documentos.partials.modal-renovar-soat', [
    'doc' => $doc,
    'vehiculo' => $vehiculo,
    'modalId' => 'modalRenovarSOAT_' . $doc->id_doc_vehiculo
    ])
    @endif

    @if($doc->tipo_documento === 'Tecnomecanica')
    @include('vehiculos.documentos.partials.modal-renovar-tecnomecanica', [
    'doc' => $doc,
    'vehiculo' => $vehiculo,
    'modalId' => 'modalRenovarTecno_' . $doc->id_doc_vehiculo
    ])
    @endif
    @endforeach

    {{-- ================= HISTÓRICO (COLAPSABLE) ================= --}}
    @if($documentosHistoricos->isNotEmpty())
    <div class="card shadow-lg border-0">
        <div class="card-header bg-secondary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa-solid fa-box-archive me-2"></i>
                    Documentos Históricos ({{ $documentosHistoricos->count() }} versiones reemplazadas)
                </h5>
                <button class="btn btn-sm btn-outline-light"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseHistorico">
                    <i class="fa-solid fa-chevron-down"></i> Ver/Ocultar
                </button>
            </div>
        </div>
        <div class="collapse" id="collapseHistorico">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Versión</th>
                                <th>Número</th>
                                <th>Emisión</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Reemplazado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentosHistoricos->sortByDesc('created_at') as $h)
                            <tr>
                                <td>{{ str_replace('_', ' ', $h->tipo_documento) }}</td>
                                <td><span class="badge bg-secondary">v{{ $h->version }}</span></td>
                                <td>{{ $h->numero_documento }}</td>
                                <td>{{ $h->fecha_emision ? \Carbon\Carbon::parse($h->fecha_emision)->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($h->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($h->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $h->estado }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($h->updated_at)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>

</div>

{{-- ESTILOS --}}
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>

@endsection