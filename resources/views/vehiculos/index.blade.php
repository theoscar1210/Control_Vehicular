@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- ================= ENCABEZADO ================= --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-cars me-2" style="color:#5B8238;"></i>
                Gestión de Vehículos
            </h3>
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-list-check me-1"></i>
                Total: <strong>{{ $vehiculos->total() }}</strong> vehículo(s)
            </p>
        </div>

        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <div class="d-flex gap-2">
            {{-- Botón Papelera - Solo ADMIN --}}
            @if(Auth::user()->rol === 'ADMIN' && isset($eliminadosCount) && $eliminadosCount > 0)
            <a href="{{ route('vehiculos.trashed') }}"
                class="btn btn-outline-danger px-3 py-2 shadow-sm position-relative"
                style="border-radius:12px;"
                data-bs-toggle="tooltip"
                title="Ver vehículos eliminados">
                <i class="fa-solid fa-trash-can me-1"></i>Papelera
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $eliminadosCount }}
                </span>
            </a>
            @endif

            {{-- Botón Nuevo --}}
            <a href="{{ route('vehiculos.create') }}"
                class="btn px-4 py-2 shadow-sm"
                style="background-color:#5B8238;color:white;border-radius:12px;">
                <i class="fa-solid fa-plus-circle me-2"></i>Nuevo Vehículo
            </a>
        </div>
        @endif
    </div>

    {{-- ================= ALERTAS ================= --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ================= TARJETA PRINCIPAL ================= --}}
    <div class="card shadow-lg border-0">
        <div class="card-header text-white py-3" style="background-color:#5B8238;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa-solid fa-table-list me-2"></i>
                    Listado de Vehículos
                </h5>

            </div>
        </div>

        <div class="card-body p-0">
            {{-- BUSCADOR --}}
            <div class="p-4 pb-3 bg-light border-bottom">
                <form method="GET" action="{{ route('vehiculos.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Buscar por placa, marca, modelo o propietario..."
                                autofocus>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color:#5B8238;">
                            <i class="fa-solid fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8f9fa; border-bottom: 2px solid #5B8238;">
                        <tr>
                            <th class="py-3">
                                <i class="fa-solid fa-id-card me-1 text-muted"></i>
                                Placa
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-car me-1 text-muted"></i>
                                Vehículo
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-user me-1 text-muted"></i>
                                Propietario
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-shield-halved me-1 text-muted"></i>
                                SOAT
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-car-side me-1 text-muted"></i>
                                Tecnomecánica
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-tags me-1 text-muted"></i>
                                Clasificación
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-toggle-on me-1 text-muted"></i>
                                Estado
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-gear me-1 text-muted"></i>
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                        @php
                        $soat = $vehiculo->estado_soat;
                        $tecno = $vehiculo->estado_tecno;
                        @endphp

                        <tr class="vehiculo-row">
                            {{-- PLACA --}}
                            <td class="fw-bold" style="color:#5B8238;">
                                {{ $vehiculo->placa }}
                            </td>

                            {{-- VEHÍCULO --}}
                            <td>
                                <div class="fw-semibold">{{ $vehiculo->marca }}</div>
                                <small class="text-muted">{{ $vehiculo->tipo }}</small>
                            </td>

                            {{-- PROPIETARIO --}}
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}">

                                    {{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}
                                </div>
                            </td>

                            {{-- SOAT --}}
                            <td class="text-center">
                                @if($soat['estado'] === 'SIN_REGISTRO')
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-ban me-1"></i>Sin registro
                                </span>
                                @else
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-{{ $soat['clase'] }} px-3 py-2">
                                        @if($soat['estado'] === 'VIGENTE')
                                        <i class="fa-solid fa-check-circle me-1"></i>Vigente
                                        @elseif($soat['estado'] === 'POR_VENCER')
                                        <i class="fa-solid fa-clock me-1"></i>{{ $soat['dias'] }}d
                                        @else
                                        <i class="fa-solid fa-times-circle me-1"></i>Vencido
                                        @endif
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $soat['fecha']->format('d/m/Y') }}
                                    </small>
                                </div>
                                @endif
                            </td>

                            {{-- TECNOMECÁNICA --}}
                            <td class="text-center">
                                @if($tecno['estado'] === 'SIN_REGISTRO')
                                @php
                                $requiereTecnoVeh = $vehiculo->requiereTecnomecanica();
                                $fechaPrimeraRevVeh = $vehiculo->fechaPrimeraTecnomecanica();
                                @endphp
                                @if($vehiculo->fecha_matricula && !$requiereTecnoVeh)
                                {{-- Vehículo nuevo exento por tiempo --}}
                                <span class="badge bg-success px-2 py-2" data-bs-toggle="tooltip"
                                    title="Primera revisión: {{ $fechaPrimeraRevVeh?->format('d/m/Y') }}">
                                    <i class="fa-solid fa-shield-check me-1"></i>Nuevo
                                </span>
                                <small class="d-block text-success" style="font-size: 0.7rem;">
                                    Exento
                                </small>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-ban me-1"></i>Sin registro
                                </span>
                                @endif
                                @else
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-{{ $tecno['clase'] }} px-3 py-2">
                                        @if($tecno['estado'] === 'VIGENTE')
                                        <i class="fa-solid fa-check-circle me-1"></i>Vigente
                                        @elseif($tecno['estado'] === 'POR_VENCER')
                                        <i class="fa-solid fa-clock me-1"></i>{{ $tecno['dias'] }}d
                                        @else
                                        <i class="fa-solid fa-times-circle me-1"></i>Vencido
                                        @endif
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $tecno['fecha']->format('d/m/Y') }}
                                    </small>
                                </div>
                                @endif
                            </td>

                            {{-- CLASIFICACIÓN --}}
                            <td class="text-center">
                                @php
                                    $clsBadge = match($vehiculo->clasificacion) {
                                        'EMPLEADO' => 'primary',
                                        'CONTRATISTA' => 'warning',
                                        'EXTERNO' => 'info',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $clsBadge }} px-2 py-1">
                                    {{ ucfirst(strtolower($vehiculo->clasificacion ?? 'N/A')) }}
                                </span>
                            </td>

                            {{-- ESTADO VEHÍCULO --}}
                            <td class="text-center">
                                @if($vehiculo->estado === 'Activo')
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fa-solid fa-circle-check me-1"></i>Activo
                                </span>
                                @else
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fa-solid fa-circle-pause me-1"></i>Inactivo
                                </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Historial --}}
                                    <a href="{{ route('vehiculos.documentos.historial.completo', $vehiculo->id_vehiculo) }}"
                                        class="btn btn-sm shadow-sm"
                                        style="background-color:#E8F5E9; color:#2E7D32; border-radius:8px;"
                                        data-bs-toggle="tooltip"
                                        title="Ver documentos">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>

                                    {{-- Editar --}}
                                    @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
                                    <a href="{{ route('vehiculos.edit', $vehiculo->id_vehiculo) }}"
                                        class="btn btn-sm shadow-sm"
                                        style="background-color:#E3F2FD; color:#1976D2; border-radius:8px;"
                                        data-bs-toggle="tooltip"
                                        title="Editar vehículo">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id_vehiculo) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm shadow-sm"
                                            style="background-color:#FFEBEE; color:#D32F2F; border-radius:8px;"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar vehículo"
                                            onclick="return confirm('¿Estás seguro de eliminar este vehículo?\n\nEsta acción no se puede deshacer.')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                    <h5>No se encontraron vehículos</h5>
                                    <p class="mb-0">
                                        @if(request('search'))
                                        No hay resultados para "{{ request('search') }}"
                                        @else
                                        No hay vehículos registrados en el sistema
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINACIÓN --}}
        @if($vehiculos->hasPages())
        <div class="card-footer bg-light border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $vehiculos->firstItem() }} - {{ $vehiculos->lastItem() }} de {{ $vehiculos->total() }}
                </div>
                <div>
                    {{ $vehiculos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        <i class="fa-regular fa-copyright me-1"></i>
        2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>

</div>

{{-- ESTILOS --}}
<style>
    .vehiculo-row {
        transition: background-color 0.2s ease-in-out;
    }

    .vehiculo-row:hover {
        background-color: #f8fdf5 !important;
    }

    .vehiculo-row:hover td {
        box-shadow: inset 0 0 0 9999px rgba(91, 130, 56, 0.03);
    }

    .btn:hover {
        opacity: 0.85;
        transition: opacity 0.2s ease-in-out;
    }

    .input-group-text {
        border-right: 0;
    }

    .form-control:focus {
        border-color: #5B8238;
        box-shadow: 0 0 0 0.2rem rgba(91, 130, 56, 0.25);
    }

    /* Estabilizar tabla para evitar temblor en scroll */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        table-layout: auto;
        min-width: 100%;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85rem;
        }

        .d-flex.gap-2 {
            flex-direction: column !important;
            gap: 0.5rem !important;
        }
    }
</style>

{{-- SCRIPTS --}}
<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>

@endsection