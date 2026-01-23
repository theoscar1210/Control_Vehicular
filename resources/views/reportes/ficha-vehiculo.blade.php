@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp



@extends('layouts.app')

@section('title', 'Ficha Vehículo - ' . $vehiculo->placa)

@section('content')
<br><br><br>
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reportes.centro') }}" style="color: #5B8238;">Reportes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reportes.vehiculos') }}" style="color: #5B8238;">Vehículos</a></li>
                    <li class="breadcrumb-item active">{{ $vehiculo->placa }}</li>
                </ol>
            </nav>
            <h2 class="mb-0"><i class="fas fa-id-badge me-2" style="color: #5B8238;"></i>Ficha del Vehículo</h2>
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
                        <div class="placa-badge bg-white text-dark rounded px-4 py-2">
                            <h2 class="mb-0 fw-bold">{{ $vehiculo->placa }}</h2>
                        </div>
                    </div>
                    <div class="col">
                        <h4 class="mb-0">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h4>
                        <small>{{ $vehiculo->tipo }} - Color: {{ $vehiculo->color }}</small>
                    </div>
                    <div class="col-auto text-end">
                        <small>Fecha de Registro</small>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($vehiculo->fecha_registro)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Información del Propietario --}}
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user-tie me-2" style="color: #5B8238;"></i>Propietario
                        </h5>
                        @if($vehiculo->propietario)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nombre:</td>
                                <td class="fw-medium">{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Documento:</td>
                                <td>{{ $vehiculo->propietario->tipo_doc }} {{ $vehiculo->propietario->identificacion }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted"><i class="fas fa-info-circle me-1"></i> Sin propietario registrado</p>
                        @endif
                    </div>

                    {{-- Información del Conductor --}}
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2" style="color: #5B8238;"></i>Conductor Asignado
                        </h5>
                        @if($vehiculo->conductor)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nombre:</td>
                                <td class="fw-medium">{{ $vehiculo->conductor->nombre }} {{ $vehiculo->conductor->apellido }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Documento:</td>
                                <td>{{ $vehiculo->conductor->tipo_doc }} {{ $vehiculo->conductor->identificacion }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Teléfono:</td>
                                <td>{{ $vehiculo->conductor->telefono ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tel. Emergencia:</td>
                                <td>{{ $vehiculo->conductor->telefono_emergencia ?? 'N/A' }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted"><i class="fas fa-info-circle me-1"></i> Sin conductor asignado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de Documentos con Semáforo --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2" style="color: #5B8238;"></i>Estado de Documentación</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($estadosDocumentos as $tipo => $info)
                    @php
                        $esExentoTecno = false;
                        $fechaPrimeraRevFicha = null;
                        if ($tipo === 'Tecnomecanica' && !$info['documento']) {
                            $requiereTecnoFicha = $vehiculo->requiereTecnomecanica();
                            $fechaPrimeraRevFicha = $vehiculo->fechaPrimeraTecnomecanica();
                            $esExentoTecno = $vehiculo->fecha_matricula && !$requiereTecnoFicha;
                        }
                        $claseCard = $esExentoTecno ? 'success' : $info['clase'];
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-{{ $claseCard }} border-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">
                                        @php
                                        $iconos = [
                                        'SOAT' => 'fas fa-shield-alt',
                                        'Tecnomecanica' => 'fas fa-tools',
                                        'Tarjeta Propiedad' => 'fas fa-credit-card',
                                        'Póliza' => 'fas fa-file-contract',
                                        'conductor_Licencia Conducción' => 'fas fa-id-card'
                                        ];
                                        $icono = $iconos[$tipo] ?? 'fas fa-file';
                                        $nombreTipo = str_replace('conductor_', '', $tipo);
                                        @endphp
                                        <i class="{{ $icono }} me-1"></i>
                                        {{ $nombreTipo }}
                                    </h6>
                                    {{-- Semáforo --}}
                                    <span class="semaforo semaforo-{{ $claseCard }}"></span>
                                </div>

                                @if($info['documento'])
                                <table class="table table-sm table-borderless small mb-0">
                                    <tr>
                                        <td class="text-muted">Número:</td>
                                        <td class="fw-medium">{{ $info['documento']->numero_documento }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Emisión:</td>
                                        <td>{{ \Carbon\Carbon::parse($info['documento']->fecha_emision)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Vencimiento:</td>
                                        <td class="fw-bold text-{{ $info['clase'] }}">
                                            {{ \Carbon\Carbon::parse($info['documento']->fecha_vencimiento)->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                    @if($info['documento']->entidad_emisora)
                                    <tr>
                                        <td class="text-muted">Entidad:</td>
                                        <td>{{ $info['documento']->entidad_emisora }}</td>
                                    </tr>
                                    @endif
                                </table>
                                <div class="mt-2">
                                    <span class="badge bg-{{ $info['clase'] }}">
                                        {{ $info['mensaje'] }}
                                    </span>
                                </div>
                                @elseif($esExentoTecno)
                                {{-- Vehículo Nuevo - Exención por tiempo --}}
                                <div class="text-center py-2">
                                    <i class="fas fa-shield-check fa-2x text-success mb-2"></i>
                                    <p class="text-success fw-bold mb-1">
                                        Vehículo "Nuevo"
                                    </p>
                                    <p class="small text-success mb-2">(Exención por tiempo)</p>
                                    <span class="badge bg-success">
                                        <i class="fas fa-calendar me-1"></i>
                                        Primera revisión: {{ $fechaPrimeraRevFicha?->format('d/m/Y') }}
                                    </span>
                                </div>
                                @else
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Documento no registrado
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Historial Reciente --}}
        @if($historialReciente->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2" style="color: #5B8238;"></i>Historial de Documentos (Últimos 10)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="px-4">Tipo</th>
                                <th>Número</th>
                                <th>Fecha Emisión</th>
                                <th>Fecha Vencimiento</th>
                                <th>Estado</th>
                                <th>Versión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historialReciente as $doc)
                            <tr class="text-muted">
                                <td class="px-4">{{ $doc->tipo_documento }}</td>
                                <td>{{ $doc->numero_documento }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $doc->estado }}</span>
                                </td>
                                <td>v{{ $doc->version }}</td>
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
            <p class="mb-0">Sistema de Control Vehicular - Club Campestre Altos del Chicalá</p>
        </div>
    </div>
</div>

<style>
    /* Semáforo de estados */
    .semaforo {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    .semaforo-success {
        background-color: #28a745;
    }

    .semaforo-warning {
        background-color: #ffc107;
    }

    .semaforo-danger {
        background-color: #dc3545;
    }

    .semaforo-secondary {
        background-color: #6c757d;
    }

    .placa-badge {
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }

    /* Estilos de impresión */
    @media print {
        .no-print {
            display: none !important;
        }

        .sidebar,
        .navbar,
        .topbar {
            display: none !important;
        }

        .content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            break-inside: avoid;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .ficha-container {
            max-width: 100% !important;
        }

        body {
            font-size: 12px;
        }

        .print-footer {
            display: block !important;
        }
    }

    @media screen {
        .print-footer {
            display: none;
        }
    }
</style>

<script>
    function exportarPDF() {
        window.open('{{ route("reportes.ficha.pdf", $vehiculo->id_vehiculo) }}', '_blank');
    }
</script>
@endsection