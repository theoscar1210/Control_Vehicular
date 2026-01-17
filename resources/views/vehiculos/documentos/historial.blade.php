@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;

// Separar documentos activos de históricos
$documentosActivos = $historial->where('activo', 1);
$documentosHistoricos = $historial->where('activo', 0);

// Contar estados
$vigentes = $documentosActivos->where('estado', 'VIGENTE')->count();
$porVencer = $documentosActivos->where('estado', 'POR_VENCER')->count();
$vencidos = $documentosActivos->where('estado', 'VENCIDO')->count();
@endphp

@extends('layouts.app')

@section('title', 'Documentos del Vehículo')

@section('content')
<br><br><br>
<div class="container-fluid py-4">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}" style="color:#5B8238;">Vehículos</a></li>
            <li class="breadcrumb-item active">Documentos {{ $vehiculo->placa }}</li>
        </ol>
    </nav>

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark">
                <i class="fa-solid fa-file-lines me-2"></i>
                Documentos del Vehículo
            </h3>
            <p class="text-muted mb-0">
                <strong>{{ $vehiculo->placa }}</strong> - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
            </p>
        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if($historial->isEmpty())
    {{-- SIN DOCUMENTOS --}}
    <div class="card shadow-lg border-0">
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-inbox text-muted" style="font-size: 4rem;"></i>
            <h5 class="text-muted mt-3">No hay documentos registrados</h5>
            <p class="text-muted">
                Este vehículo no tiene documentos registrados aún.
            </p>
        </div>
    </div>
    @else
    {{-- RESUMEN ESTADÍSTICO --}}
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

    {{-- DOCUMENTOS ACTIVOS --}}
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header text-white" style="background-color:#5B8238;">
            <h5 class="mb-0">
                <i class="fa-solid fa-file-circle-check me-2"></i>
                Documentos Activos
            </h5>
        </div>
        <div class="card-body p-4">
            @if($documentosActivos->isEmpty())
            <div class="text-center py-4">
                <i class="fa-solid fa-folder-open text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No hay documentos activos</p>
            </div>
            @else
            <div class="row">
                @foreach($documentosActivos as $doc)
                @php
                $esTarjetaPropiedad = str_replace(' ', '_', $doc->tipo_documento) === 'Tarjeta_Propiedad';
                $dias = null;

                if (!$esTarjetaPropiedad) {
                $dias = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay(), false);
                }
                @endphp

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 
                                @if($esTarjetaPropiedad) border-info
                                @elseif($doc->estado == 'VIGENTE') border-success
                                @elseif($doc->estado == 'POR_VENCER') border-warning
                                @else border-danger
                                @endif" style="border-width: 2px;">

                        {{-- HEADER DEL DOCUMENTO --}}
                        <div class="card-header 
                                    @if($esTarjetaPropiedad) bg-info text-white
                                    @elseif($doc->estado == 'VIGENTE') bg-success text-white
                                    @elseif($doc->estado == 'POR_VENCER') bg-warning text-dark
                                    @else bg-danger text-white
                                    @endif">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fa-solid fa-file-alt me-1"></i>
                                    {{ str_replace('_', ' ', $doc->tipo_documento) }}
                                </h6>
                                <span class="badge 
                                            @if($esTarjetaPropiedad) bg-white text-info
                                            @elseif($doc->estado == 'VIGENTE') bg-white text-success
                                            @elseif($doc->estado == 'POR_VENCER') bg-dark text-warning
                                            @else bg-white text-danger
                                            @endif">
                                    v{{ $doc->version }}
                                </span>
                            </div>
                        </div>

                        {{-- BODY DEL DOCUMENTO --}}
                        <div class="card-body">
                            <p class="mb-2">
                                <strong><i class="fa-solid fa-hashtag me-1 text-muted"></i>Número:</strong><br>
                                <span class="ms-3">{{ $doc->numero_documento }}</span>
                            </p>

                            @if($doc->entidad_emisora)
                            <p class="mb-2">
                                <strong><i class="fa-solid fa-building me-1 text-muted"></i>Entidad:</strong><br>
                                <span class="ms-3">{{ $doc->entidad_emisora }}</span>
                            </p>
                            @endif

                            <p class="mb-2">
                                <strong><i class="fa-solid fa-calendar-plus me-1 text-muted"></i>Emisión:</strong><br>
                                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}</span>
                            </p>

                            @if(!$esTarjetaPropiedad)
                            <p class="mb-2">
                                <strong><i class="fa-solid fa-calendar-xmark me-1 text-muted"></i>Vencimiento:</strong><br>
                                <span class="ms-3">{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</span>

                                @if($dias !== null)
                                <br>
                                <span class="badge mt-1 
                                                @if($dias > 30) bg-success
                                                @elseif($dias > 0) bg-warning text-dark
                                                @else bg-danger
                                                @endif">
                                    @if($dias > 0)
                                    <i class="fa-solid fa-clock"></i> {{ $dias }} días restantes
                                    @elseif($dias == 0)
                                    <i class="fa-solid fa-exclamation-circle"></i> Vence hoy
                                    @else
                                    <i class="fa-solid fa-times-circle"></i> Vencido hace {{ abs($dias) }} días
                                    @endif
                                </span>
                                @endif
                            </p>
                            @else
                            <div class="alert alert-info py-2 px-3 mb-2" role="alert">
                                <small>
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Documento permanente (sin vencimiento)
                                </small>
                            </div>
                            @endif

                            @if($doc->nota)
                            <p class="mb-2">
                                <strong><i class="fa-solid fa-note-sticky me-1 text-muted"></i>Nota:</strong><br>
                                <span class="ms-3"><em class="text-muted small">{{ $doc->nota }}</em></span>
                            </p>
                            @endif

                            <hr>

                            <p class="mb-0 small text-muted">
                                <i class="fa-solid fa-clock me-1"></i>
                                Registrado: {{ \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y H:i') }}
                                @if($doc->creado_por)
                                <br><i class="fa-solid fa-user me-1"></i>
                                Por: {{ $doc->creador->nombre ?? 'Usuario #'.$doc->creado_por }}
                                @endif
                            </p>
                        </div>

                        {{-- FOOTER CON ACCIONES --}}
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between gap-2">
                                {{-- Botón de historial --}}
                                <button
                                    class="btn btn-sm btn-outline-secondary flex-fill btn-historial"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHistorial"
                                    data-doc="{{ $doc->id_doc_vehiculo }}">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span class="d-none d-lg-inline"> Historial</span>
                                </button>

                                {{-- Botón de renovar (solo si NO es Tarjeta de Propiedad) --}}
                                @if(!$esTarjetaPropiedad)
                                @if(in_array($doc->estado, ['VENCIDO', 'POR_VENCER']))
                                <a href="{{ route('vehiculos.documentos.edit', [$vehiculo->id_vehiculo, $doc->id_doc_vehiculo]) }}"
                                    class="btn btn-sm text-white flex-fill"
                                    style="background-color:#5B8238;">
                                    <i class="fa-solid fa-arrow-rotate-right"></i> Renovar
                                </a>
                                @else
                                <a href="{{ route('vehiculos.documentos.edit', [$vehiculo->id_vehiculo, $doc->id_doc_vehiculo]) }}"
                                    class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fa-solid fa-pencil"></i>
                                    <span class="d-none d-lg-inline"> Actualizar</span>
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL DE HISTORIAL POR DOCUMENTO --}}
                <div class="modal fade" id="modalHistorial{{ $doc->id_doc_vehiculo }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#5B8238; color:white;">
                                <h5 class="modal-title">
                                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                                    Historial: {{ str_replace('_', ' ', $doc->tipo_documento) }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                $historialTipo = $historial->where('tipo_documento', $doc->tipo_documento)->sortByDesc('version');
                                @endphp

                                @if($historialTipo->count() > 1)
                                <div class="list-group">
                                    @foreach($historialTipo as $historico)
                                    <div class="list-group-item {{ $historico->activo ? 'list-group-item-success' : '' }}">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1">
                                                <span class="badge bg-secondary me-2">v{{ $historico->version }}</span>
                                                {{ $historico->numero_documento }}
                                                @if($historico->activo)
                                                <span class="badge" style="background-color:#5B8238;">ACTUAL</span>
                                                @endif
                                            </h6>
                                            <small>
                                                @if($historico->estado == 'VIGENTE')
                                                <span class="badge bg-success">VIGENTE</span>
                                                @elseif($historico->estado == 'POR_VENCER')
                                                <span class="badge bg-warning text-dark">POR VENCER</span>
                                                @elseif($historico->estado == 'VENCIDO')
                                                <span class="badge bg-danger">VENCIDO</span>
                                                @else
                                                <span class="badge bg-secondary">REEMPLAZADO</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <i class="fa-solid fa-calendar-plus"></i>
                                                    Emisión: {{ \Carbon\Carbon::parse($historico->fecha_emision)->format('d/m/Y') }}
                                                </small>
                                            </div>
                                            @if($historico->tipo_documento !== 'Tarjeta_Propiedad')
                                            <div class="col-6">
                                                <small class="text-muted">
                                                    <i class="fa-solid fa-calendar-xmark"></i>
                                                    Vence: {{ \Carbon\Carbon::parse($historico->fecha_vencimiento)->format('d/m/Y') }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                        @if($historico->nota)
                                        <p class="mb-0 mt-2">
                                            <small><em class="text-muted">{{ $historico->nota }}</em></small>
                                        </p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="fa-solid fa-info-circle text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Este documento no tiene versiones anteriores.</p>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- SECCIÓN DE DOCUMENTOS HISTÓRICOS (Opcional - Colapsable) --}}
    @if($documentosHistoricos->isNotEmpty())
    <div class="card shadow-lg border-0">
        <div class="card-header bg-secondary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa-solid fa-box-archive me-2"></i>
                    Documentos Históricos (Reemplazados)
                </h5>
                <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistoricos">
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="collapseHistoricos">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
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
                            @foreach($documentosHistoricos->sortByDesc('created_at') as $hist)
                            <tr>
                                <td>{{ str_replace('_', ' ', $hist->tipo_documento) }}</td>
                                <td><span class="badge bg-secondary">v{{ $hist->version }}</span></td>
                                <td>{{ $hist->numero_documento }}</td>
                                <td>{{ \Carbon\Carbon::parse($hist->fecha_emision)->format('d/m/Y') }}</td>
                                <td>
                                    @if($hist->tipo_documento !== 'Tarjeta_Propiedad')
                                    {{ \Carbon\Carbon::parse($hist->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $hist->estado }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($hist->updated_at)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>


<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#5B8238; color:white;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Historial del Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoHistorial">
                <div class="text-center py-4">
                    <div class="spinner-border text-success"></div>
                    <p class="mt-2 text-muted">Cargando historial...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.btn-historial').forEach(btn => {
            btn.addEventListener('click', function() {

                const docId = this.dataset.doc;
                const contenedor = document.getElementById('contenidoHistorial');

                contenedor.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success"></div>
                    <p class="mt-2 text-muted">Cargando historial...</p>
                </div>
            `;

                fetch(`/vehiculos/documentos/${docId}/historial`)
                    .then(res => res.text())
                    .then(html => contenedor.innerHTML = html)
                    .catch(() => contenedor.innerHTML =
                        `<div class="alert alert-danger">Error cargando historial</div>`
                    );
            });
        });

    });
</script>



{{-- ESTILOS --}}
<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
    }

    @media print {

        .btn,
        nav,
        footer,
        .card-footer {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
    }
</style>


@endsection