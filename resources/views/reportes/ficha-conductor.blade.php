@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Ficha Conductor - ' . $conductor->nombre . ' ' . $conductor->apellido)

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reportes.conductores') }}" style="color: #5B8238;">Conductores</a></li>
                    <li class="breadcrumb-item active">{{ $conductor->nombre }} {{ $conductor->apellido }}</li>
                </ol>
            </nav>
            <h2 class="mb-0">Ficha del Conductor</h2>
        </div>

        <div>
            <a href="{{ route('reportes.conductores') }}" class="btn btn-universal">
                <i class="fas fa-arrow-left me-1"></i> Volver
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
            <button type="button" class="btn btn-danger" onclick="exportarPDF()">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
        </div>
    </div>

    {{-- Ficha Imprimible --}}
    <div class="ficha-container">
        {{-- Encabezado de Ficha --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header py-3 text-white" style="background: linear-gradient(135deg, #5B8238 0%, #7da956 100%);">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar-circle-lg bg-white text-dark">
                            {{ strtoupper(substr($conductor->nombre, 0, 1)) }}{{ strtoupper(substr($conductor->apellido, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-0">{{ $conductor->nombre }} {{ $conductor->apellido }}</h3>
                        <small>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}</small>
                    </div>
                    <div class="col-auto text-end">
                        @if($conductor->clasificacion)
                        <span class="badge bg-{{ $conductor->clasificacion_badge }} px-3 py-2 mb-1">
                            <i class="fas fa-tags me-1"></i>{{ $conductor->clasificacion_label }}
                        </span><br>
                        @endif
                        <span class="badge bg-{{ $estadoGeneral['clase'] }} px-3 py-2">
                            <i class="{{ $estadoGeneral['icono'] }} me-1"></i>
                            {{ $estadoGeneral['texto'] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Informacion Personal --}}
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2" style="color: #5B8238;"></i>Informacion Personal
                        </h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nombre Completo:</td>
                                <td class="fw-medium">{{ $conductor->nombre }} {{ $conductor->apellido }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Documento:</td>
                                <td>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telefono:</td>
                                <td>{{ $conductor->telefono ?? 'No registrado' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tel. Emergencia:</td>
                                <td>{{ $conductor->telefono_emergencia ?? 'No registrado' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Estado:</td>
                                <td>
                                    @if($conductor->activo)
                                    <span class="badge bg-success">Activo</span>
                                    @else
                                    <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Clasificación:</td>
                                <td>
                                    <span class="badge bg-{{ $conductor->clasificacion_badge }}">{{ $conductor->clasificacion_label }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Vehiculo Asignado --}}
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-car me-2" style="color: #5B8238;"></i>Vehiculo(s) Asignado(s)
                        </h5>
                        @if($conductor->vehiculos->count() > 0)
                        @foreach($conductor->vehiculos as $vehiculo)
                        <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                            <div class="placa-badge bg-white border rounded px-3 py-1 me-3">
                                <strong style="color: #5B8238;">{{ $vehiculo->placa }}</strong>
                            </div>
                            <div>
                                <div class="fw-medium">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                <small class="text-muted">{{ $vehiculo->tipo }} - {{ $vehiculo->color }}</small>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="text-muted"><i class="fas fa-info-circle me-1"></i> Sin vehiculo asignado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de Licencia de Conduccion --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Licencia de Conduccion</h5>
            </div>
            <div class="card-body">
                @if($licencia)
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-{{ $licencia->clase_badge }} border-2 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-id-card me-1"></i> Datos de la Licencia
                                    </h6>
                                    <span class="semaforo semaforo-{{ $licencia->clase_badge }}"></span>
                                </div>
                                <table class="table table-sm table-borderless small mb-0">
                                    <tr>
                                        <td class="text-muted">Numero:</td>
                                        <td class="fw-bold">{{ $licencia->numero_documento ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Expedicion:</td>
                                        <td>
                                            @if($licencia->fecha_emision)
                                            {{ $licencia->fecha_emision->format('d/m/Y') }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                    @if($licencia->entidad_emisora)
                                    <tr>
                                        <td class="text-muted">Entidad:</td>
                                        <td>{{ $licencia->entidad_emisora }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Categorias y Vencimientos --}}
                    <div class="col-md-8">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-layer-group me-1" style="color: #5B8238;"></i> Categorias y Vencimientos
                                </h6>

                                <div class="row">
                                    {{-- Categoria Principal --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-{{ $licencia->clase_badge }} h-100">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-info fs-6">{{ $licencia->categoria_licencia ?? 'N/A' }}</span>
                                                    <small class="text-muted">Principal</small>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">Vencimiento:</small>
                                                    @if($licencia->fecha_vencimiento)
                                                    <div class="fw-bold text-{{ $licencia->clase_badge }}">
                                                        {{ $licencia->fecha_vencimiento->format('d/m/Y') }}
                                                    </div>
                                                    <small class="text-{{ $licencia->clase_badge }}">
                                                        ({{ $licencia->diasRestantes() }} dias)
                                                    </small>
                                                    @else
                                                    <div class="text-muted">Sin fecha</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Categorias Adicionales --}}
                                    @if($licencia->categorias_adicionales)
                                    @php
                                    $categoriasAdicionales = explode(',', $licencia->categorias_adicionales);
                                    $fechasPorCategoria = $licencia->fechas_por_categoria ?? [];
                                    @endphp
                                    @foreach($categoriasAdicionales as $catAdicional)
                                    @php
                                    $catAdicional = trim($catAdicional);
                                    $fechaVencCat = $fechasPorCategoria[$catAdicional]['fecha_vencimiento'] ?? null;
                                    $estadoCat = 'secondary';
                                    $diasCat = null;

                                    if ($fechaVencCat) {
                                    $fechaVencCatCarbon = \Carbon\Carbon::parse($fechaVencCat);
                                    $diasCat = (int) now()->startOfDay()->diffInDays($fechaVencCatCarbon->startOfDay(), false);

                                    if ($diasCat < 0) {
                                        $estadoCat='danger' ;
                                        } elseif ($diasCat <=20) {
                                        $estadoCat='warning' ;
                                        } else {
                                        $estadoCat='success' ;
                                        }
                                        }
                                        @endphp
                                        <div class="col-md-6 mb-3">
                                        <div class="card border-{{ $estadoCat }} h-100">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-info fs-6">{{ $catAdicional }}</span>
                                                    <small class="text-muted">Adicional</small>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">Vencimiento:</small>
                                                    @if($fechaVencCat)
                                                    <div class="fw-bold text-{{ $estadoCat }}">
                                                        {{ \Carbon\Carbon::parse($fechaVencCat)->format('d/m/Y') }}
                                                    </div>
                                                    <small class="text-{{ $estadoCat }}">
                                                        ({{ $diasCat }} dias)
                                                    </small>
                                                    @else
                                                    <div class="text-muted">Sin fecha registrada</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estado General de Licencia --}}
            <div class="alert alert-{{ $licencia->clase_badge }} mb-0">
                <div class="d-flex align-items-center">
                    <i class="{{ $estadoGeneral['icono'] }} fa-2x me-3"></i>
                    <div>
                        <strong>{{ $licencia->estado_legible }}</strong>
                        @if($licencia->fecha_vencimiento)
                        <p class="mb-0 small">
                            La licencia principal vence el {{ $licencia->fecha_vencimiento->format('d/m/Y') }}
                            ({{ $licencia->diasRestantes() }} dias)
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Sin licencia registrada.</strong> Este conductor no tiene licencia de conduccion en el sistema.
            </div>
            @endif
        </div>
    </div>

    {{-- Historial de Documentos --}}
    @if($historialDocumentos->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Documentos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4">Tipo</th>
                            <th>Numero</th>
                            <th>Fecha Emision</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                            <th>Version</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historialDocumentos as $doc)
                        <tr class="{{ $doc->activo ? '' : 'text-muted' }}">
                            <td class="px-4">{{ $doc->tipo_documento }}</td>
                            <td>{{ $doc->numero_documento ?? '-' }}</td>
                            <td>{{ $doc->fecha_emision ? $doc->fecha_emision->format('d/m/Y') : '-' }}</td>
                            <td>{{ $doc->fecha_vencimiento ? $doc->fecha_vencimiento->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $doc->activo ? $doc->clase_badge : 'secondary' }}">
                                    {{ $doc->activo ? $doc->estado : 'INACTIVO' }}
                                </span>
                            </td>
                            <td>v{{ $doc->version ?? 1 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Pie de Ficha --}}
    <div class="text-center text-muted small py-3 print-footer">
        <hr>
        <p class="mb-1">Ficha generada el {{ now()->format('d/m/Y H:i') }}</p>
        <p class="mb-0">Sistema de Control Vehicular - Club Campestre Altos del Chicala</p>
    </div>
</div>
</div>

<script>
    function exportarPDF() {
        window.open('{{ route("reportes.ficha.conductor.pdf", $conductor->id_conductor) }}', '_blank');
    }
</script>
@endsection