@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@section('content')

{{-- HEADER --}}
<div class="container py-3">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <div>
            <h3 class="mb-0">
                <i class="bi bi-person-lines-fill"></i> Editar Conductor
            </h3>
            <p class="text-muted mb-0 small">{{ $conductor->nombre }} {{ $conductor->apellido }}</p>
        </div>
        <a href="{{ route('conductores.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Regresar
        </a>
    </div>
</div>

<div class="container pb-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i><strong>Por favor corrija los siguientes errores:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('conductores.update', $conductor) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- =========================================== --}}
        {{-- SECCIÓN 1: DATOS PERSONALES                --}}
        {{-- =========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-conductor rounded-top-4 py-3">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-person-fill me-2"></i>Datos Personales
                </h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $conductor->nombre) }}"
                            class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido', $conductor->apellido) }}"
                            class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Tipo Documento</label>
                        <select name="tipo_doc" class="form-select rounded-3 border-success-subtle">
                            <option value="CC" {{ old('tipo_doc', $conductor->tipo_doc) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('tipo_doc', $conductor->tipo_doc) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Identificación</label>
                        <input type="text" name="identificacion" value="{{ old('identificacion', $conductor->identificacion) }}"
                            class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $conductor->telefono) }}"
                            class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Teléfono de Emergencia</label>
                        <input type="text" name="telefono_emergencia" value="{{ old('telefono_emergencia', $conductor->telefono_emergencia) }}"
                            class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tags me-1 text-muted"></i>Clasificación
                        </label>
                        <select name="clasificacion" class="form-select rounded-3 border-success-subtle">
                            <option value="EMPLEADO" {{ old('clasificacion', $conductor->clasificacion ?? 'EMPLEADO') == 'EMPLEADO' ? 'selected' : '' }}>Empleado</option>
                            <option value="CONTRATISTA" {{ old('clasificacion', $conductor->clasificacion) == 'CONTRATISTA' ? 'selected' : '' }}>Contratista</option>
                            <option value="EXTERNO" {{ old('clasificacion', $conductor->clasificacion) == 'EXTERNO' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch ps-5">
                            <input class="form-check-input border-success" type="checkbox"
                                name="activo" value="1" id="activo" role="switch"
                                {{ old('activo', $conductor->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">Conductor Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================== --}}
        {{-- SECCIÓN 2: ASIGNACIÓN DE VEHÍCULOS         --}}
        {{-- =========================================== --}}
        @php
            $vehiculosAsignadosIds = old('vehiculos_ids',
                $conductor->vehiculosAsignados->pluck('id_vehiculo')->toArray()
            );
        @endphp
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-conductor rounded-top-4 py-3">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-car-front me-2"></i>Asignación de Vehículos
                </h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Vehículos Asignados <span class="text-muted fw-normal">(opcional — puede seleccionar varios)</span>
                        </label>
                        <select name="vehiculos_ids[]" id="select-vehiculos-edit" class="form-select rounded-3 border-success-subtle" multiple>
                            @foreach($vehiculos as $v)
                            <option value="{{ $v->id_vehiculo }}"
                                data-placa="{{ $v->placa }}"
                                data-marca="{{ $v->marca }}"
                                data-modelo="{{ $v->modelo ?? '' }}"
                                data-color="{{ $v->color ?? '' }}"
                                data-propietario="{{ $v->propietario->nombre ?? '' }} {{ $v->propietario->apellido ?? '' }}"
                                {{ in_array($v->id_vehiculo, $vehiculosAsignadosIds) ? 'selected' : '' }}>
                                {{ $v->placa }} — {{ $v->marca }} {{ $v->modelo ?? '' }}
                                @if($v->propietario)
                                ({{ $v->propietario->nombre }} {{ $v->propietario->apellido }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>El primer vehículo seleccionado será el principal. Puedes seleccionar varios.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================== --}}
        {{-- SECCIÓN 3: DOCUMENTACIÓN                   --}}
        {{-- =========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-conductor rounded-top-4 py-3">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-file-earmark-text me-2"></i>Documentación
                </h5>
            </div>
            <div class="card-body p-3 p-md-4">

                <p class="text-muted mb-3">Historial de documentos del conductor</p>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Número</th>
                                <th>Versión</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documentos as $doc)
                            <tr class="{{ $doc->activo ? 'fw-semibold' : 'text-muted' }}">
                                <td>{{ $doc->tipo_documento }}</td>
                                <td>
                                    @if($doc->categoria_licencia)
                                    <span class="badge bg-info">{{ $doc->categoria_licencia }}</span>
                                    @if($doc->categorias_adicionales)
                                    <small class="text-muted d-block">+{{ $doc->categorias_adicionales }}</small>
                                    @endif
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $doc->numero_documento }}</td>
                                <td>{{ $doc->version }}</td>
                                <td>{{ optional($doc->fecha_registro)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $doc->clase_badge }}">
                                        @if($doc->estado === 'VIGENTE')
                                        <i class="bi bi-check-circle me-1"></i>
                                        @elseif($doc->estado === 'POR_VENCER')
                                        <i class="bi bi-clock me-1"></i>
                                        @elseif($doc->estado === 'VENCIDO')
                                        <i class="bi bi-x-circle me-1"></i>
                                        @else
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        @endif
                                        {{ $doc->estado_legible }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay documentos registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Categorías Monitoreadas --}}
                @php
                    $licenciaActiva = $documentos->where('tipo_documento', 'LICENCIA CONDUCCION')->where('activo', true)->first();
                @endphp
                @if($licenciaActiva)
                <div class="border rounded-3 p-3 mb-4 bg-warning-subtle">
                    <h6 class="fw-semibold text-dark mb-3">
                        <i class="bi bi-bell me-1"></i>Categorías a Monitorear para Alertas
                    </h6>
                    <p class="text-muted small mb-2">
                        Solo se generarán alertas de vencimiento para las categorías seleccionadas.
                        Actualmente monitoreando:
                        @if($licenciaActiva->categorias_monitoreadas)
                            <strong>{{ implode(', ', $licenciaActiva->categorias_monitoreadas) }}</strong>
                        @else
                            <strong>Todas las categorías</strong> (por defecto)
                        @endif
                    </p>
                    <div class="row">
                        @php
                            $todasCategorias = $licenciaActiva->todas_categorias;
                            $categoriasMonitoreadas = $licenciaActiva->categorias_monitoreadas ?? $todasCategorias;
                        @endphp
                        @foreach($todasCategorias as $cat)
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    name="categorias_monitoreadas[]" value="{{ $cat }}" id="mon_edit_{{ $cat }}"
                                    {{ in_array($cat, $categoriasMonitoreadas) ? 'checked' : '' }}>
                                <label class="form-check-label {{ $cat === $licenciaActiva->categoria_licencia ? 'fw-bold' : '' }}" for="mon_edit_{{ $cat }}">
                                    {{ $cat }}
                                    @if($cat === $licenciaActiva->categoria_licencia)
                                    <small class="text-muted">(principal)</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Acción sobre documentación --}}
                <h6 class="fw-semibold text-secondary mb-3">Acción sobre documentación</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Acción</label>
                    <select name="documento_action" class="form-select rounded-3 border-success-subtle">
                        <option value="none" {{ old('documento_action') == 'none' ? 'selected' : '' }}>No hacer nada</option>
                        <option value="update_existing" {{ old('documento_action') == 'update_existing' ? 'selected' : '' }}>Actualizar documento existente (solo metadata)</option>
                        <option value="create_version" {{ old('documento_action') == 'create_version' ? 'selected' : '' }}>Crear nueva versión (sin archivo)</option>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Documento existente</label>
                        <select name="documento_id" class="form-select rounded-3 border-success-subtle">
                            <option value="">-- Seleccionar --</option>
                            @foreach($documentos as $doc)
                            <option value="{{ $doc->id_doc_conductor }}" {{ old('documento_id') == $doc->id_doc_conductor ? 'selected' : '' }}>
                                {{ $doc->tipo_documento }} — v{{ $doc->version }} — {{ $doc->numero_documento ?? '—' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="documento_tipo" id="documento_tipo_edit" class="form-select rounded-3 border-success-subtle">
                            <option value="">-- Seleccionar --</option>
                            <option value="LICENCIA CONDUCCION" {{ old('documento_tipo') == 'LICENCIA CONDUCCION' ? 'selected' : '' }}>Licencia Conducción</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="seccion_categoria_edit" style="display: none;">
                        <label class="form-label fw-semibold">Categoría de Licencia</label>
                        <select name="categoria_licencia" id="categoria_licencia_edit" class="form-select rounded-3 border-success-subtle">
                            <option value="">-- Seleccionar --</option>
                            <optgroup label="Motocicletas">
                                <option value="A1" {{ old('categoria_licencia') == 'A1' ? 'selected' : '' }}>A1 - Motocicletas hasta 125cc</option>
                                <option value="A2" {{ old('categoria_licencia') == 'A2' ? 'selected' : '' }}>A2 - Motocicletas más de 125cc</option>
                            </optgroup>
                            <optgroup label="Vehículos Particulares">
                                <option value="B1" {{ old('categoria_licencia') == 'B1' ? 'selected' : '' }}>B1 - Automóviles, Camperos, Camionetas</option>
                                <option value="B2" {{ old('categoria_licencia') == 'B2' ? 'selected' : '' }}>B2 - Camiones, Buses</option>
                                <option value="B3" {{ old('categoria_licencia') == 'B3' ? 'selected' : '' }}>B3 - Vehículos Articulados</option>
                            </optgroup>
                            <optgroup label="Servicio Público">
                                <option value="C1" {{ old('categoria_licencia') == 'C1' ? 'selected' : '' }}>C1 - Taxi</option>
                                <option value="C2" {{ old('categoria_licencia') == 'C2' ? 'selected' : '' }}>C2 - Bus/Buseta Público</option>
                                <option value="C3" {{ old('categoria_licencia') == 'C3' ? 'selected' : '' }}>C3 - Carga Pública</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número Documento</label>
                        <input name="documento_numero" value="{{ old('documento_numero') }}" class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Emisión</label>
                        <input type="date" name="documento_fecha_emision" value="{{ old('documento_fecha_emision') }}" class="form-control rounded-3 border-success-subtle">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Vencimiento</label>
                        <input type="date" name="documento_fecha_vencimiento" value="{{ old('documento_fecha_vencimiento') }}" class="form-control rounded-3 border-success-subtle">
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-2 mb-3">
            <button type="submit" class="btn btn-universal px-4 py-2 rounded-3">
                <i class="bi bi-save me-2"></i>Actualizar Conductor
            </button>
            <a href="{{ route('conductores.index') }}" class="btn btn-outline-danger px-4 py-2 rounded-3">
                <i class="bi bi-x-circle me-1"></i>Cancelar
            </a>
        </div>

    </form>
</div>

<script src="{{ asset('js/conductor-edit.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    function formatVehiculo(v) {
        if (!v.id) return v.text;
        return $(
            '<div class="d-flex align-items-center py-2">' +
            '<div class="me-3"><i class="bi bi-car-front-fill fs-4" style="color:#5B8238;"></i></div>' +
            '<div><div class="fw-bold">' + ($(v.element).data('placa') || '') + '</div>' +
            '<small class="text-muted">' + ($(v.element).data('marca') || '') + ' ' + ($(v.element).data('modelo') || '') + '</small><br>' +
            '<small class="text-muted"><i class="bi bi-person-fill me-1"></i>' + ($(v.element).data('propietario') || '') + '</small>' +
            '</div></div>'
        );
    }

    $('#select-vehiculos-edit').select2({
        theme: 'bootstrap-5',
        placeholder: '🔍 Buscar por placa, marca o propietario...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'No se encontraron vehículos'; },
            searching: function () { return 'Buscando...'; }
        },
        templateResult: formatVehiculo,
        templateSelection: function (v) {
            if (!v.id) return v.text;
            var placa = $(v.element).data('placa') || v.text;
            return $('<span><i class="bi bi-car-front-fill me-1" style="color:#5B8238;"></i>' + placa + '</span>');
        }
    });
});
</script>

<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-color: rgba(var(--bs-success-rgb), 0.3) !important;
        min-height: 38px;
    }
    .select2-container--bootstrap-5 .select2-selection:focus,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #5B8238 !important;
        box-shadow: 0 0 0 0.25rem rgba(91, 130, 56, 0.25) !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: rgba(var(--bs-success-rgb), 0.3) !important;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #5B8238 !important;
        color: white !important;
    }
</style>

@endsection
