@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Historial de Documentos')

@section('content')
<br><br><br>
<div class="container-fluid py-4">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}" style="color:#5B8238;">Vehículos</a></li>
            <li class="breadcrumb-item active">Historial {{ $vehiculo->placa }}</li>
        </ol>
    </nav>

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>
                Historial de documentos del vehículo
            </h3>


        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-universal px-3 py-2" style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-1 "></i> Volver
        </a>

    </div>


    @if($historial->isEmpty())
    {{-- SIN HISTORIAL --}}
    <div class="card shadow-lg border-0">
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-inbox text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-muted mt-3">No hay historial disponible</h5>
            <p class="text-muted">
                No se encontraron registros de <strong>{{ $tipoDocumento }}</strong> para este vehículo.
            </p>
        </div>
    </div>
    @else
    {{-- RESUMEN ESTADÍSTICO --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold" style="color:#5B8238;">{{ $historial->count() }}</h2>
                    <small class="text-muted">Versiones Totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-success">{{ $historial->where('estado', 'VIGENTE')->count() }}</h2>
                    <small class="text-muted">Vigentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-warning">{{ $historial->where('estado', 'POR_VENCER')->count() }}</h2>
                    <small class="text-muted">Por Vencer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <h2 class="mb-0 fw-bold text-danger">{{ $historial->where('estado', 'VENCIDO')->count() + $historial->where('estado', 'REEMPLAZADO')->count() }}</h2>
                    <small class="text-muted">Vencidos/Reemplazados</small>
                </div>
            </div>
        </div>
    </div>

    {{-- TIMELINE DE VERSIONES --}}
    <div class="card shadow-lg border-0">
        <div class="card-header text-white" style="background-color:#5B8238;">
            <h5 class="mb-0">
                <i class="fa-solid fa-list-ol me-2"></i>
                Línea de Tiempo de Versiones
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="timeline">
                @foreach($historial as $doc)
                <div class="timeline-item {{ $loop->first ? 'active' : '' }} mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row">
                        {{-- VERSIÓN Y ESTADO --}}
                        <div class="col-md-2 text-center">
                            <div class="timeline-badge 
                                        @if($doc->estado == 'VIGENTE') 
                                            @if($loop->first) badge-vigente-actual @else bg-success @endif
                                        @elseif($doc->estado == 'POR_VENCER') bg-warning
                                        @elseif($doc->estado == 'VENCIDO') bg-danger
                                        @else bg-secondary
                                        @endif">
                                <h3 class="mb-0 text-white fw-bold">v{{ $doc->version }}</h3>
                            </div>
                            <div class="mt-2">
                                @if($doc->estado == 'VIGENTE')
                                <span class="badge" style="background-color:#5B8238;">VIGENTE</span>
                                @elseif($doc->estado == 'POR_VENCER')
                                <span class="badge bg-warning text-dark">POR VENCER</span>
                                @elseif($doc->estado == 'VENCIDO')
                                <span class="badge bg-danger">VENCIDO</span>
                                @else
                                <span class="badge bg-secondary">REEMPLAZADO</span>
                                @endif
                            </div>
                        </div>

                        {{-- DETALLES DEL DOCUMENTO --}}
                        <div class="col-md-10">
                            <div class="card {{ $loop->first ? 'border-success shadow' : 'border-0 bg-light' }}">
                                @if($loop->first)
                                <div class="card-header text-white" style="background-color:#5B8238;">
                                    <small class="fw-semibold">
                                        <i class="fa-solid fa-star me-1"></i>Versión Actual
                                    </small>
                                </div>
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-hashtag me-1"></i>Número:</strong>
                                                {{ $doc->numero_documento }}
                                            </p>
                                            @if($doc->entidad_emisora)
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-building me-1"></i>Entidad:</strong>
                                                {{ $doc->entidad_emisora }}
                                            </p>
                                            @endif
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-calendar-plus me-1"></i>Emisión:</strong>
                                                {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-calendar-xmark me-1"></i>Vencimiento:</strong>
                                                {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                                @php
                                                $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento), false);
                                                @endphp
                                                @if($dias >= 0 && $doc->estado != 'REEMPLAZADO')
                                                <span class="badge 
                                                                @if($dias > 30) bg-success
                                                                @elseif($dias > 0) bg-warning text-dark
                                                                @else bg-danger
                                                                @endif">
                                                    {{ $dias }} días restantes
                                                </span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-clock me-1"></i>Creado:</strong>
                                                {{ \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y H:i') }}
                                            </p>
                                            @if($doc->creado_por)
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-user me-1"></i>Por:</strong>
                                                {{ $doc->creador->nombre_completo }}

                                            </p>


                                            @endif
                                            @if($doc->nota)
                                            <p class="mb-2">
                                                <strong><i class="fa-solid fa-note-sticky me-1"></i>Nota:</strong>
                                                <em class="text-muted">{{ $doc->nota }}</em>
                                            </p>
                                            @endif
                                            @if($doc->reemplazado_por)
                                            <p class="mb-2 text-muted">
                                                <small>
                                                    <i class="fa-solid fa-arrow-right me-1"></i>
                                                    Reemplazado por versión {{ $historial->where('id_doc_vehiculo', $doc->reemplazado_por)->first()->version ?? '?' }}
                                                </small>
                                            </p>
                                            @endif
                                        </div>
                                    </div>

                                    @if($loop->first && in_array($doc->estado, ['VENCIDO', 'POR_VENCER']))
                                    <div class="mt-3">
                                        <a href="{{ route('vehiculos.documentos.edit', [$vehiculo->id_vehiculo, $doc->id_doc_vehiculo]) }}"
                                            class="btn px-3 text-white" style="background-color:#5B8238; border-radius:10px;">
                                            <i class="fa-solid fa-arrow-rotate-right me-1"></i> Renovar Ahora
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BOTÓN PARA IMPRIMIR --}}
    <div class="mt-3 text-end">
        <button onclick="window.print()" class="btn btn-outline-secondary px-4" style="border-radius:10px;">
            <i class="fa-solid fa-print me-1"></i> Imprimir Historial
        </button>
    </div>
    @endif

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>

{{-- ESTILOS --}}
<style>
    .timeline {
        position: relative;
    }

    .timeline-badge {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .badge-vigente-actual {
        background: linear-gradient(135deg, #5B8238 0%, #4a6b2d 100%);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(91, 130, 56, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(91, 130, 56, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(91, 130, 56, 0);
        }
    }

    @media print {

        .btn,
        nav,
        footer {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline-badge {
            width: 70px;
            height: 70px;
        }

        .timeline-badge h3 {
            font-size: 1.5rem;
        }
    }
</style>

@endsection