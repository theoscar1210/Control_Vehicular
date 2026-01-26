@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Vehículos Eliminados')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- ================= ENCABEZADO ================= --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-trash-can me-2 text-danger"></i>
                Vehículos Eliminados
            </h3>
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-info-circle me-1"></i>
                Los vehículos aquí listados se eliminarán permanentemente después de 6 meses
            </p>
        </div>

        <a href="{{ route('vehiculos.index') }}"
            class="btn btn-secondary px-4 py-2 shadow-sm"
            style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    {{-- ================= ALERTAS ================= --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <strong>Exito!</strong> {{ session('success') }}
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
        <div class="card-header text-white py-3 bg-danger">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa-solid fa-recycle me-2"></i>
                    Papelera de Vehículos
                </h5>
                <span class="badge bg-light text-danger">
                    {{ $vehiculos->total() }} vehículo(s)
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- BUSCADOR --}}
            <div class="p-4 pb-3 bg-light border-bottom">
                <form method="GET" action="{{ route('vehiculos.trashed') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Buscar por placa, marca o modelo..."
                                autofocus>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100 shadow-sm">
                            <i class="fa-solid fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8f9fa; border-bottom: 2px solid #dc3545;">
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
                                <i class="fa-solid fa-calendar-xmark me-1 text-muted"></i>
                                Fecha Eliminación
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-clock me-1 text-muted"></i>
                                Días Restantes
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
                            $diasRestantes = 180 - (int) now()->diffInDays($vehiculo->deleted_at);
                            $diasRestantes = max(0, $diasRestantes);
                        @endphp

                        <tr>
                            {{-- PLACA --}}
                            <td class="fw-bold text-danger">
                                {{ $vehiculo->placa }}
                            </td>

                            {{-- VEHÍCULO --}}
                            <td>
                                <div class="fw-semibold">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                <small class="text-muted">{{ $vehiculo->tipo }} - {{ $vehiculo->color }}</small>
                            </td>

                            {{-- PROPIETARIO --}}
                            <td>
                                @if($vehiculo->propietario)
                                {{ $vehiculo->propietario->nombre }} {{ $vehiculo->propietario->apellido }}
                                @else
                                <span class="text-muted">Sin propietario</span>
                                @endif
                            </td>

                            {{-- FECHA ELIMINACIÓN --}}
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    {{ $vehiculo->deleted_at->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $vehiculo->deleted_at->diffForHumans() }}</small>
                            </td>

                            {{-- DÍAS RESTANTES --}}
                            <td class="text-center">
                                @if($diasRestantes <= 30)
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                    {{ $diasRestantes }} días
                                </span>
                                @elseif($diasRestantes <= 90)
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    {{ $diasRestantes }} días
                                </span>
                                @else
                                <span class="badge bg-success px-3 py-2">
                                    {{ $diasRestantes }} días
                                </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Restaurar --}}
                                    <form action="{{ route('vehiculos.restore', $vehiculo->id_vehiculo) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-success shadow-sm"
                                            style="border-radius:8px;"
                                            data-bs-toggle="tooltip"
                                            title="Restaurar vehículo"
                                            onclick="return confirm('¿Restaurar este vehículo?')">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-trash-can fs-1 mb-3 d-block" style="opacity:0.3;"></i>
                                    <h5>No hay vehículos eliminados</h5>
                                    <p class="mb-0">La papelera está vacía</p>
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

{{-- SCRIPTS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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
