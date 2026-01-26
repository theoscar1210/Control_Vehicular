@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp

@extends('layouts.app')

@section('title', 'Conductores Eliminados')

@section('content')
<br><br><br>

<div class="container-fluid py-4">

    {{-- ================= ENCABEZADO ================= --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-trash-can me-2 text-danger"></i>
                Conductores Eliminados
            </h3>
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-info-circle me-1"></i>
                Los conductores aqui listados se eliminaran permanentemente despues de 6 meses
            </p>
        </div>

        <a href="{{ route('conductores.index') }}"
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
                    Papelera de Conductores
                </h5>
                <span class="badge bg-light text-danger">
                    {{ $conductores->total() }} conductor(es)
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- BUSCADOR --}}
            <div class="p-4 pb-3 bg-light border-bottom">
                <form method="GET" action="{{ route('conductores.trashed') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="Buscar por nombre, apellido o identificacion..."
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
                                <i class="fa-solid fa-user me-1 text-muted"></i>
                                Conductor
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-id-card me-1 text-muted"></i>
                                Identificacion
                            </th>
                            <th class="py-3">
                                <i class="fa-solid fa-phone me-1 text-muted"></i>
                                Telefono
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-calendar-xmark me-1 text-muted"></i>
                                Fecha Eliminacion
                            </th>
                            <th class="py-3 text-center">
                                <i class="fa-solid fa-clock me-1 text-muted"></i>
                                Dias Restantes
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
                            $diasRestantes = 180 - (int) now()->diffInDays($conductor->deleted_at);
                            $diasRestantes = max(0, $diasRestantes);
                        @endphp

                        <tr>
                            {{-- CONDUCTOR --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="background-color:#FFEBEE; color:#D32F2F;">
                                        {{ strtoupper(substr($conductor->nombre, 0, 1)) }}{{ strtoupper(substr($conductor->apellido, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-danger">{{ $conductor->nombre }} {{ $conductor->apellido }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- IDENTIFICACION --}}
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $conductor->tipo_doc }}
                                </span>
                                <span class="fw-medium ms-1">{{ $conductor->identificacion }}</span>
                            </td>

                            {{-- TELEFONO --}}
                            <td>
                                @if($conductor->telefono)
                                {{ $conductor->telefono }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- FECHA ELIMINACION --}}
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    {{ $conductor->deleted_at->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $conductor->deleted_at->diffForHumans() }}</small>
                            </td>

                            {{-- DIAS RESTANTES --}}
                            <td class="text-center">
                                @if($diasRestantes <= 30)
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                    {{ $diasRestantes }} dias
                                </span>
                                @elseif($diasRestantes <= 90)
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    {{ $diasRestantes }} dias
                                </span>
                                @else
                                <span class="badge bg-success px-3 py-2">
                                    {{ $diasRestantes }} dias
                                </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Restaurar --}}
                                    <form action="{{ route('conductores.restore', $conductor->id_conductor) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-success shadow-sm"
                                            style="border-radius:8px;"
                                            data-bs-toggle="tooltip"
                                            title="Restaurar conductor"
                                            onclick="return confirm('¿Restaurar este conductor?')">
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
                                    <h5>No hay conductores eliminados</h5>
                                    <p class="mb-0">La papelera esta vacia</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINACION --}}
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
        2025 Club Campestre Altos del Chicala. Todos los derechos reservados.
    </footer>

</div>

{{-- ESTILOS --}}
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.85rem;
    }
</style>

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
