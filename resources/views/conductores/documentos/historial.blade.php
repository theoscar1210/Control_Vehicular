@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;

// Documentos activos e históricos
$documentosActivos = $historial->where('activo', 1);
$documentosHistoricos = $historial->where('activo', 0);

// Conteo
$vigentes = $documentosActivos->filter(fn($d) => $d->estado === 'VIGENTE')->count();
$porVencer = $documentosActivos->filter(fn($d) => $d->estado === 'POR_VENCER')->count();
$vencidos = $documentosActivos->filter(fn($d) => $d->estado === 'VENCIDO')->count();

// Licencia activa
$licencia = $documentosActivos->where('tipo_documento', 'Licencia Conducción')->first();
@endphp

@extends('layouts.app')
@section('title','Documentos del Conductor')

@section('content')
<br><br><br>
<div class="container-fluid py-4 historial-documentos">

    {{-- ================= ENCABEZADO ================= --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('conductores.index') }}" style="color:#5B8238;">Conductores</a>
            </li>
            <li class="breadcrumb-item active">Documentos {{ $conductor->nombre }} {{ $conductor->apellido }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">
                <i class="fa-solid fa-file-lines me-2"></i>
                Documentos del Conductor
            </h3>
            <p class="text-muted mb-0">
                <strong>{{ $conductor->nombre }} {{ $conductor->apellido }}</strong> — {{ $conductor->tipo_doc }} {{ $conductor->identificacion }}
            </p>
        </div>
        <a href="{{ route('conductores.index') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
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
                    <small class="text-muted">Por Vencer (≤20 días)</small>
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
                {{-- LICENCIA DE CONDUCCIÓN --}}
                @if($doc->tipo_documento === 'Licencia Conducción')
                @include('conductores.documentos.partials.card-licencia', [
                    'doc' => $doc,
                    'conductor' => $conductor
                ])
                @else
                {{-- OTROS DOCUMENTOS (EPS, ARL, etc.) --}}
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card doc-card h-100 border-{{ $doc->clase_badge }}" style="border-width: 2px;">
                        <div class="card-header bg-{{ $doc->clase_badge }} text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fa-solid fa-file-alt me-1"></i>
                                {{ $doc->tipo_documento }}
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
                            @if($doc->fecha_vencimiento)
                            <p class="mb-2">
                                <strong>Vencimiento:</strong> {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                            </p>
                            @endif
                            @if($doc->entidad_emisora)
                            <p class="mb-0">
                                <strong>Entidad:</strong> {{ $doc->entidad_emisora }}
                            </p>
                            @endif
                        </div>
                        <div class="card-footer bg-light text-center">
                            <span class="badge bg-{{ $doc->clase_badge }} py-2 px-3">
                                {{ $doc->estado_legible }}
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

    {{-- ================= MODAL DE RENOVACIÓN ================= --}}
    @if($licencia && in_array($licencia->estado, ['VENCIDO', 'POR_VENCER']))
    @include('conductores.documentos.partials.modal-renovar-licencia', [
        'doc' => $licencia,
        'conductor' => $conductor,
        'modalId' => 'modalRenovarLicencia_' . $licencia->id_doc_conductor
    ])
    @endif

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
                                <th>Categorías</th>
                                <th>Número</th>
                                <th>Vencimiento</th>
                                <th>Motivo</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentosHistoricos->sortByDesc('version') as $h)
                            <tr>
                                <td>{{ $h->tipo_documento }}</td>
                                <td><span class="badge bg-secondary">v{{ $h->version }}</span></td>
                                <td>
                                    @if($h->tipo_documento === 'Licencia Conducción')
                                        @php
                                            $cats = $h->todas_categorias;
                                        @endphp
                                        @foreach($cats as $cat)
                                            <span class="badge bg-dark me-1">{{ $cat }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $h->numero_documento }}</td>
                                <td>
                                    @if($h->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($h->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($h->nota)
                                        <small class="text-info">
                                            <i class="fa-solid fa-info-circle me-1"></i>{{ $h->nota }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($h->fecha_registro)
                                        {{ \Carbon\Carbon::parse($h->fecha_registro)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
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

@endsection
