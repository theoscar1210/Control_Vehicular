@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Gestión de Conductores')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- ================= ENCABEZADO ================= --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-id-card-clip me-2" style="color:#5B8238;"></i>
                Gestión de Conductores
            </h3>
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-list-check me-1"></i>
                Total: <strong>{{ $conductores->total() }}</strong> conductor(es)
            </p>
        </div>

        @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
        <div class="d-flex gap-2">
            {{-- Boton Papelera - Solo ADMIN --}}
            @if(Auth::user()->rol === 'ADMIN' && isset($eliminadosCount) && $eliminadosCount > 0)
            <a href="{{ route('conductores.trashed') }}"
                class="btn btn-outline-danger px-3 py-2 shadow-sm position-relative"
                style="border-radius:12px;"
                data-bs-toggle="tooltip"
                title="Ver conductores eliminados">
                <i class="fa-solid fa-trash-can me-1"></i>Papelera
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $eliminadosCount }}
                </span>
            </a>
            @endif

            {{-- Boton Nuevo --}}
            <a href="{{ route('conductores.create') }}"
                class="btn px-4 py-2 shadow-sm"
                style="background-color:#5B8238;color:white;border-radius:12px;">
                <i class="fa-solid fa-plus-circle me-2"></i>Nuevo Conductor
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

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fa-solid fa-exclamation-triangle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ================= TARJETA PRINCIPAL ================= --}}
    <div class="card shadow-lg border-0">
        <div class="card-header text-white py-3" style="background-color:#5B8238;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa-solid fa-table-list me-2"></i>
                    Listado de Conductores
                </h5>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- BUSCADOR --}}
            <div class="p-4 pb-3 bg-light border-bottom">
                <form method="GET" action="{{ route('conductores.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Buscar por nombre, apellido o identificación..."
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
                                <i class="fa-solid fa-user me-1 text-muted"></i>
                                Conductor
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-id-card me-1 text-muted"></i>
                                Identificación
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-phone me-1 text-muted"></i>
                                Teléfono
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-car me-1 text-muted"></i>
                                Vehículo
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-file-lines me-1 text-muted"></i>
                                Licencia
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
                        @forelse($conductores as $conductor)
                        @php
                            // Obtener vehículo asignado
                            $vehiculoAsignado = $conductor->vehiculos->first();

                            // Obtener licencia activa - usar accessors del modelo para estado y clase
                            $licencia = $conductor->documentosConductor
                                ->where('tipo_documento', 'LICENCIA CONDUCCION')
                                ->where('activo', true)
                                ->first();
                        @endphp

                        <tr class="conductor-row">
                            {{-- CONDUCTOR --}}
                            <td>
                                <div class="fw-semibold">{{ $conductor->nombre }} {{ $conductor->apellido }}</div>
                                <small class="text-muted">
                                    Registrado: {{ \Carbon\Carbon::parse($conductor->fecha_registro)->format('d/m/Y') }}
                                </small>
                            </td>

                            {{-- IDENTIFICACIÓN --}}
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $conductor->tipo_doc }}
                                </span>
                                <span class="fw-medium ms-1">{{ $conductor->identificacion }}</span>
                            </td>

                            {{-- TELÉFONO --}}
                            <td>
                                @if($conductor->telefono)
                                <div>
                                    <i class="fa-solid fa-phone text-muted me-1"></i>
                                    {{ $conductor->telefono }}
                                </div>
                                @endif
                                @if($conductor->telefono_emergencia)
                                <small class="text-muted">
                                    <i class="fa-solid fa-phone-volume me-1"></i>
                                    {{ $conductor->telefono_emergencia }}
                                </small>
                                @endif
                                @if(!$conductor->telefono && !$conductor->telefono_emergencia)
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- VEHÍCULO ASIGNADO --}}
                            <td class="text-center">
                                @if($vehiculoAsignado)
                                <a href="{{ route('vehiculos.edit', $vehiculoAsignado->id_vehiculo) }}"
                                    class="text-decoration-none"
                                    data-bs-toggle="tooltip"
                                    title="{{ $vehiculoAsignado->marca }} {{ $vehiculoAsignado->modelo }}">
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fa-solid fa-car me-1"></i>
                                        {{ $vehiculoAsignado->placa }}
                                    </span>
                                </a>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-ban me-1"></i>Sin asignar
                                </span>
                                @endif
                            </td>

                            {{-- LICENCIA - Usa accessors del modelo: $licencia->estado y $licencia->clase_badge --}}
                            <td class="text-center">
                                @if($licencia)
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-{{ $licencia->clase_badge }} px-3 py-2">
                                        @if($licencia->estado === 'VIGENTE')
                                        <i class="fa-solid fa-check-circle me-1"></i>
                                        @elseif($licencia->estado === 'POR_VENCER')
                                        <i class="fa-solid fa-clock me-1"></i>
                                        @else
                                        <i class="fa-solid fa-times-circle me-1"></i>
                                        @endif
                                        {{ $licencia->categoria_licencia ?? 'N/A' }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->format('d/m/Y') }}
                                    </small>
                                </div>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fa-solid fa-ban me-1"></i>Sin registro
                                </span>
                                @endif
                            </td>

                            {{-- CLASIFICACIÓN --}}
                            <td class="text-center">
                                <span class="badge bg-{{ $conductor->clasificacion_badge }} px-2 py-1">
                                    {{ $conductor->clasificacion_label }}
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td class="text-center">
                                @if($conductor->activo)
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
                                    {{-- Ver documentos --}}
                                    <a href="{{ route('conductores.documentos.historial', $conductor->id_conductor) }}"
                                        class="btn btn-sm shadow-sm"
                                        style="background-color:#E8F5E9; color:#2E7D32; border-radius:8px;"
                                        data-bs-toggle="tooltip"
                                        title="Ver documentos">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>

                                    {{-- Editar --}}
                                    @if (Auth::user()->rol === 'ADMIN' || Auth::user()->rol === 'SST')
                                    <a href="{{ route('conductores.edit', $conductor->id_conductor) }}"
                                        class="btn btn-sm shadow-sm"
                                        style="background-color:#E3F2FD; color:#1976D2; border-radius:8px;"
                                        data-bs-toggle="tooltip"
                                        title="Editar conductor">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('conductores.destroy', $conductor->id_conductor) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm shadow-sm"
                                            style="background-color:#FFEBEE; color:#D32F2F; border-radius:8px;"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar conductor"
                                            onclick="return confirm('¿Estás seguro de eliminar este conductor?\n\nEl conductor y sus documentos permanecerán en el sistema por 6 meses antes de ser eliminados definitivamente.')">
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
                                    <h5>No se encontraron conductores</h5>
                                    <p class="mb-0">
                                        @if(request('search'))
                                        No hay resultados para "{{ request('search') }}"
                                        @else
                                        No hay conductores registrados en el sistema
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
        @if($conductores->hasPages())
        <div class="card-footer bg-light border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $conductores->firstItem() }} - {{ $conductores->lastItem() }} de {{ $conductores->total() }}
                </div>
                <div>
                    {{ $conductores->links('pagination::bootstrap-5') }}
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
    .conductor-row {
        transition: background-color 0.2s ease-in-out;
    }

    .conductor-row:hover {
        background-color: #f8fdf5 !important;
    }

    .conductor-row:hover td {
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
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
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
