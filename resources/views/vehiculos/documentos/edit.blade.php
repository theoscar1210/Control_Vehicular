@php ($navbarEspecial = true)
@php($ocultarNavbar = true)
@php($sinPadding = true)

@extends('layouts.app')

@section('title', 'Renovar Documento')

@section('content')
<br><br><br>
<div class="container-fluid py-4">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}">Vehículos</a></li>
            <li class="breadcrumb-item active">Renovar {{ $documento->tipo_documento }}</li>
        </ol>
    </nav>

    {{-- ENCABEZADO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark">
                <i class="fa-solid fa-arrow-rotate-right me-2"></i>
                Renovar {{ $documento->tipo_documento }}
            </h3>
            <p class="text-muted mb-0">Vehículo: <strong>{{ $vehiculo->placa }}</strong> - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
        </div>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary px-3 py-2" style="border-radius:12px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- MENSAJES --}}
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

    <div class="row">
        {{-- INFORMACIÓN DEL DOCUMENTO ACTUAL --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header text-white" style="background-color:#5B8238;">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Documento Actual
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th style="width:45%;">Tipo:</th>
                            <td>{{ $documento->tipo_documento }}</td>
                        </tr>
                        <tr>
                            <th>Número:</th>
                            <td>{{ $documento->numero_documento }}</td>
                        </tr>
                        <tr>
                            <th>Versión:</th>
                            <td><span class="badge bg-secondary">v{{ $documento->version }}</span></td>
                        </tr>



                        <tr>
                            <th>Emisión:</th>
                            <td>{{ \Carbon\Carbon::parse($documento->fecha_emision)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Vencimiento:</th>
                            <td>{{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}</td>
                        </tr>
                        @if($documento->entidad_emisora)
                        <tr>
                            <th>Entidad:</th>
                            <td>{{ $documento->entidad_emisora }}</td>
                        </tr>
                        @endif
                    </table>

                    <div class="alert alert-warning mt-3" role="alert">
                        <small>
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Al renovar, este documento se marcará como REEMPLAZADO y se creará una nueva versión.
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('vehiculos.documentos.historial', [$vehiculo->id_vehiculo, $documento->tipo_documento]) }}"
                        class="btn w-100" style="background-color:#E8F5E9; color:#2E7D32; border-radius:8px;">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>Ver Historial de Versiones
                    </a>
                </div>
            </div>
        </div>

        {{-- FORMULARIO DE RENOVACIÓN --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white" style="background-color:#5B8238;">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-file-circle-plus me-2"></i>
                        Datos del Nuevo Documento
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('vehiculos.documentos.update', [$vehiculo->id_vehiculo, $documento->id_doc_vehiculo]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- NÚMERO DE DOCUMENTO --}}
                            <div class="col-md-6 mb-3">
                                <label for="numero_documento" class="form-label fw-semibold">
                                    Número de Documento <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    id="numero_documento"
                                    name="numero_documento"
                                    value="{{ old('numero_documento', $documento->numero_documento) }}"
                                    required>
                                @error('numero_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ENTIDAD EMISORA --}}
                            <div class="col-md-6 mb-3">
                                <label for="entidad_emisora" class="form-label fw-semibold">Entidad Emisora</label>
                                <input type="text"
                                    class="form-control @error('entidad_emisora') is-invalid @enderror"
                                    id="entidad_emisora"
                                    name="entidad_emisora"
                                    value="{{ old('entidad_emisora', $documento->entidad_emisora) }}">
                                @error('entidad_emisora')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- FECHA DE EMISIÓN --}}
                            <div class="col-md-6 mb-3">
                                <label for="fecha_emision" class="form-label fw-semibold">
                                    Fecha de Emisión <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control @error('fecha_emision') is-invalid @enderror"
                                    id="fecha_emision"
                                    name="fecha_emision"
                                    value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                    required>
                                @error('fecha_emision')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fa-solid fa-circle-info me-1"></i>
                                    El vencimiento se calculará automáticamente (+1 año)
                                </small>
                            </div>

                            {{-- FECHA DE VENCIMIENTO CALCULADA --}}
                            <div class="col-md-6 mb-3">
                                <label for="fecha_vencimiento_calc" class="form-label fw-semibold">
                                    Fecha de Vencimiento (calculada)
                                </label>
                                <input type="text"
                                    class="form-control bg-light"
                                    id="fecha_vencimiento_calc"
                                    readonly
                                    placeholder="Se calculará automáticamente">
                                <small class="form-text text-muted">
                                    <i class="fa-solid fa-calculator me-1"></i>
                                    Calculado: Emisión + 1 año
                                </small>
                            </div>
                        </div>

                        {{-- NOTA --}}
                        <div class="mb-4">
                            <label for="nota" class="form-label fw-semibold">Nota / Observaciones</label>
                            <textarea class="form-control @error('nota') is-invalid @enderror"
                                id="nota"
                                name="nota"
                                rows="3"
                                placeholder="Ej: Renovación programada, cambio de entidad emisora, etc.">{{ old('nota') }}</textarea>
                            @error('nota')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('vehiculos.index') }}"
                                class="btn btn-secondary px-4"
                                style="border-radius:10px;">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                            </a>
                            <button type="submit"
                                class="btn px-4 text-white"
                                style="background-color:#5B8238; border-radius:10px;">
                                <i class="fa-solid fa-check me-1"></i> Renovar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </footer>
</div>

{{-- SCRIPTS --}}
<script>
    // Calcular fecha de vencimiento automáticamente
    document.getElementById('fecha_emision').addEventListener('change', function() {
        const fechaEmision = new Date(this.value);
        if (!isNaN(fechaEmision)) {
            // Agregar 1 año
            fechaEmision.setFullYear(fechaEmision.getFullYear() + 1);

            // Formatear fecha
            const year = fechaEmision.getFullYear();
            const month = String(fechaEmision.getMonth() + 1).padStart(2, '0');
            const day = String(fechaEmision.getDate()).padStart(2, '0');

            document.getElementById('fecha_vencimiento_calc').value = `${day}/${month}/${year}`;
        }
    });

    // Calcular al cargar si ya hay fecha
    if (document.getElementById('fecha_emision').value) {
        document.getElementById('fecha_emision').dispatchEvent(new Event('change'));
    }
</script>

@endsection