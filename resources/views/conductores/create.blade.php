@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@section('content')

{{-- HEADER --}}
<div class="container py-3">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <div>
            <h3 class="mb-0">
                <i class="bi bi-person-lines-fill"></i> Crear Conductor
            </h3>
            <p class="text-muted mb-0 small">Ingrese la información del conductor</p>
        </div>
        <a href="{{ route('conductores.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Regresar
        </a>
    </div>
</div>

<div class="container pb-4">

    {{-- ALERTAS --}}
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
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('conductores.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. Juan Carlos">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. Pérez Gómez">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Tipo de Documento</label>
                        <select name="tipo_doc" class="form-select rounded-3 border-success-subtle">
                            <option value="">Seleccionar</option>
                            <option value="CC" {{ old('tipo_doc')=='CC'?'selected':'' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('tipo_doc')=='CE'?'selected':'' }}>Cédula de Extranjería</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Identificación</label>
                        <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. 1234567890">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. 3001234567">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Teléfono Emergencia</label>
                        <input type="text" name="telefono_emergencia"
                            value="{{ old('telefono_emergencia') }}"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. 3109876543">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Número de un familiar o contacto de emergencia
                        </small>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Clasificación</label>
                        <select name="clasificacion" class="form-select rounded-3 border-success-subtle">
                            @foreach($clasificaciones as $clas)
                            <option value="{{ $clas }}" {{ old('clasificacion', 'EMPLEADO') === $clas ? 'selected' : '' }}>{{ $clas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch ps-5">
                            <input class="form-check-input border-success" type="checkbox"
                                name="activo" value="1" id="activo" role="switch"
                                {{ old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">
                                Conductor Activo
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones <span class="text-muted fw-normal">(opcional)</span></label>
                        <textarea name="observaciones" class="form-control rounded-3 border-success-subtle" rows="3"
                            placeholder="Notas adicionales sobre el conductor...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================== --}}
        {{-- SECCIÓN 2: ASIGNACIÓN DE VEHÍCULO          --}}
        {{-- =========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-conductor rounded-top-4 py-3">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-car-front me-2"></i>Asignación de Vehículo
                </h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Seleccionar Vehículo <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <select name="id_vehiculo" id="select-vehiculo" class="form-select rounded-3 border-success-subtle">
                            <option value="">--- Sin asignar ---</option>
                            @foreach($vehiculos as $veh)
                            <option value="{{ $veh->id_vehiculo }}"
                                data-placa="{{ $veh->placa }}"
                                data-marca="{{ $veh->marca }}"
                                data-modelo="{{ $veh->modelo ?? '' }}"
                                data-color="{{ $veh->color ?? '' }}"
                                data-propietario="{{ $veh->propietario->nombre ?? '' }} {{ $veh->propietario->apellido ?? '' }}"
                                {{ old('id_vehiculo') == $veh->id_vehiculo ? 'selected' : '' }}>
                                {{ $veh->placa }} - {{ $veh->marca }} {{ $veh->modelo ?? '' }}
                                @if($veh->propietario)
                                ({{ $veh->propietario->nombre }} {{ $veh->propietario->apellido }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-search me-1"></i>Busca por placa, marca, modelo o propietario
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================== --}}
        {{-- SECCIÓN 3: LICENCIA DE CONDUCCIÓN          --}}
        {{-- =========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-conductor rounded-top-4 py-3">
                <h5 class="mb-0 text-white">
                    <i class="bi bi-file-earmark-text me-2"></i>Licencia de Conducción
                </h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">

                    {{-- Fila 1: Tipo documento, número, entidad --}}
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label fw-semibold">Tipo de Documento</label>
                        <select name="documento_tipo" id="documento_tipo" class="form-select rounded-3 border-success-subtle">
                            <option value="LICENCIA CONDUCCION">Licencia Conducción</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label fw-semibold">Número Documento</label>
                        <input type="text" name="documento_numero"
                            class="form-control rounded-3 border-success-subtle"
                            value="{{ old('documento_numero') }}"
                            placeholder="Ej. 1234567890">
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <label class="form-label fw-semibold">Entidad Emisora</label>
                        <input type="text" name="entidad_emisora"
                            class="form-control rounded-3 border-success-subtle"
                            placeholder="Ej. Secretaría de Tránsito"
                            value="{{ old('entidad_emisora') }}">
                    </div>

                    {{-- Fila 2: Categoría principal y fecha expedición --}}
                    <div class="col-sm-6" id="seccion_categorias">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-card-checklist me-1"></i>Categoría Principal
                        </label>
                        <select name="categoria_licencia" id="categoria_licencia" class="form-select rounded-3 border-success-subtle">
                            <option value="">Seleccionar categoría</option>
                            <optgroup label="Motocicletas">
                                <option value="A1" {{ old('categoria_licencia')=='A1'?'selected':'' }}>A1 - Motocicletas hasta 125cc</option>
                                <option value="A2" {{ old('categoria_licencia')=='A2'?'selected':'' }}>A2 - Motocicletas más de 125cc</option>
                            </optgroup>
                            <optgroup label="Vehículos Particulares">
                                <option value="B1" {{ old('categoria_licencia')=='B1'?'selected':'' }}>B1 - Automóviles, Camperos, Camionetas</option>
                                <option value="B2" {{ old('categoria_licencia')=='B2'?'selected':'' }}>B2 - Camiones, Buses</option>
                                <option value="B3" {{ old('categoria_licencia')=='B3'?'selected':'' }}>B3 - Vehículos Articulados</option>
                            </optgroup>
                            <optgroup label="Servicio Público">
                                <option value="C1" {{ old('categoria_licencia')=='C1'?'selected':'' }}>C1 - Taxi</option>
                                <option value="C2" {{ old('categoria_licencia')=='C2'?'selected':'' }}>C2 - Bus/Buseta Público</option>
                                <option value="C3" {{ old('categoria_licencia')=='C3'?'selected':'' }}>C3 - Carga Pública</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Fecha de Expedición</label>
                        <input type="date" name="documento_fecha_emision" id="documento_fecha_emision"
                            class="form-control rounded-3 border-success-subtle"
                            value="{{ old('documento_fecha_emision') }}">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Fecha de expedición de la licencia
                        </small>
                    </div>

                    {{-- Categorías Adicionales --}}
                    <div class="col-12" id="seccion_categorias_adicionales">
                        <label class="form-label fw-semibold">Categorías Adicionales <span class="text-muted fw-normal">(opcional)</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['A1','A2','B1','B2','B3','C1','C2','C3'] as $cat)
                            <div class="form-check">
                                <input class="form-check-input categoria-adicional" type="checkbox"
                                    name="categorias_adicionales[]" value="{{ $cat }}" id="cat_{{ $cat }}"
                                    {{ in_array($cat, old('categorias_adicionales', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_{{ $cat }}">{{ $cat }}</label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Si selecciona categorías adicionales, podrá ingresar la fecha de vencimiento para cada una
                        </small>
                    </div>

                    {{-- Categorías a Monitorear para Alertas --}}
                    <div class="col-12" id="seccion_categorias_monitoreadas">
                        <div class="border rounded-3 p-3 bg-warning-subtle">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="bi bi-bell me-1"></i>Categorías a Monitorear para Alertas
                            </label>
                            <div class="d-flex flex-wrap gap-3" id="checkboxes_monitoreadas">
                                {{-- Se llena dinámicamente con JavaScript --}}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Solo se generarán alertas de vencimiento para las categorías seleccionadas
                            </small>
                        </div>
                    </div>

                    {{-- Vencimiento categoría principal --}}
                    <div class="col-sm-6" id="vencimiento_principal">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <label class="form-label fw-semibold text-success">
                                <i class="bi bi-calendar-check me-1"></i>
                                Vencimiento Categoría Principal
                                <span id="label_categoria_principal" class="badge bg-success ms-1"></span>
                            </label>
                            <input type="date" name="documento_fecha_vencimiento" id="documento_fecha_vencimiento"
                                class="form-control rounded-3 border-success-subtle"
                                value="{{ old('documento_fecha_vencimiento') }}">
                        </div>
                    </div>

                    {{-- Contenedor para vencimientos de categorías adicionales (dinámico) --}}
                    <div class="col-sm-6" id="fechas_adicionales_container"></div>

                    {{-- Adjuntar archivo (solo SST/ADMIN y clasificación EMPLEADO) --}}
                    @if(in_array(auth()->user()->rol, ['ADMIN', 'SST']))
                    <div class="col-12" id="seccion_archivo_conductor">
                        <div class="border rounded-3 p-3 bg-light">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-paperclip me-1"></i>Adjuntar Documento
                            </label>
                            <input type="file" name="archivo" class="form-control rounded-3 border-success-subtle"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">
                                <i class="bi bi-cloud-arrow-up me-1"></i>Se almacenará en Google Drive. Máx. 10MB.
                            </small>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-2 mb-3">
            <button type="submit" class="btn btn-universal px-4 py-2 rounded-3">
                <i class="bi bi-check-circle me-2"></i>Crear Conductor
            </button>
            <a href="{{ route('conductores.index') }}" class="btn btn-outline-danger px-4 py-2 rounded-3">
                <i class="bi bi-x-circle me-1"></i>Cancelar
            </a>
        </div>

    </form>

</div>

{{-- Scripts de Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/conductor-form.js') }}"></script>


<style>
    /* Estilos personalizados para Select2 */
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

    .select2-container--bootstrap-5 .select2-search__field {
        border-color: rgba(var(--bs-success-rgb), 0.3) !important;
    }

    .select2-container--bootstrap-5 .select2-search__field:focus {
        border-color: #5B8238 !important;
        outline: none !important;
    }
</style>

@endsection