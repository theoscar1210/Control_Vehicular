@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@section('content')

<div class="inicio-conductor">
    <h3>
        <i class="bi bi-person-lines-fill"></i> Crear Conductor

    </h3>
    <p>Ingrese la información del conductor</p>

</div>
<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card shadow border-0 rounded-4">
                <div class="card-header  py-3 rounded-top-4">
                    <h4 class="mb-0 text-center">
                        <i class="bi bi-person-plus-fill me-2"></i>Información del Conductor
                    </h4>
                </div>

                <div class="card-body">

                    {{-- SUCCESS --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{-- ERRORS --}}
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif


                    <form action="{{ route('conductores.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">

                            {{-- ========================== --}}
                            {{-- COLUMNA IZQUIERDA      --}}
                            {{-- ========================== --}}
                            <div class="col-xl-8">

                                <div class="p-3 border rounded-4 shadow-sm color-card">



                                    <div class="row g-3 color-card">

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nombre</label>
                                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                                class="form-control rounded-3 border-success-subtle">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Apellido</label>
                                            <input type="text" name="apellido" value="{{ old('apellido') }}"
                                                class="form-control rounded-3 border-success-subtle">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tipo de Documento</label>
                                            <select name="tipo_doc" class="form-select rounded-3 border-success-subtle">
                                                <option value="">Seleccionar</option>
                                                <option value="CC" {{ old('tipo_doc')=='CC'?'selected':'' }}>Cédula de Ciudadanía</option>
                                                <option value="CE" {{ old('tipo_doc')=='CE'?'selected':'' }}>Cédula de Extranjería</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Identificación</label>
                                            <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                                                class="form-control rounded-3 border-success-subtle">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Teléfono</label>
                                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                                class="form-control rounded-3 border-success-subtle">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Teléfono Emergencia</label>
                                            <input type="text" name="telefono_emergencia"
                                                value="{{ old('telefono_emergencia') }}"
                                                class="form-control rounded-3 border-success-subtle">
                                            <small class="text-muted">Número de un familiar o contacto de emergencia</small>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input border-success" type="checkbox"
                                                    name="activo" value="1" id="activo"
                                                    {{ old('activo', true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="activo">
                                                    Activo
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-car-front me-1"></i>Asignar a Vehículo
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


                            {{-- ========================== --}}
                            {{-- COLUMNA DERECHA     --}}
                            {{-- ========================== --}}
                            <div class="col-xl-4">

                                <div class="card shadow-sm rounded-4 border-success-subtle">
                                    <div class="card-header bg-conductor rounded-top-4">
                                        <h5 class="title-licencia ">
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            Documento Licencia de Conducción
                                        </h5>
                                    </div>

                                    <div class=" color-card">

                                        <div class="row g-3 color-card">

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Tipo de Documento</label>
                                                <select name="documento_tipo" id="documento_tipo" class="form-select rounded-3 border-success-subtle">
                                                    <option value="Licencia Conducción">Licencia Conducción</option>
                                                    {{-- Opciones deshabilitadas para futuras actualizaciones --}}
                                                    {{-- <option value="Certificado Médico">Certificado Médico</option> --}}
                                                    {{-- <option value="ARL">ARL</option> --}}
                                                    {{-- <option value="EPS">EPS</option> --}}
                                                    {{-- <option value="Otro">Otro</option> --}}
                                                </select>
                                            </div>

                                            {{-- Categorías de Licencia (solo visible para Licencia Conducción) --}}
                                            <div class="col-12" id="seccion_categorias">
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

                                            {{-- Categorías Adicionales --}}
                                            <div class="col-12" id="seccion_categorias_adicionales">
                                                <label class="form-label fw-semibold">Categorías Adicionales (opcional)</label>
                                                <div class="row">
                                                    @foreach(['A1','A2','B1','B2','B3','C1','C2','C3'] as $cat)
                                                    <div class="col-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input categoria-adicional" type="checkbox"
                                                                name="categorias_adicionales[]" value="{{ $cat }}" id="cat_{{ $cat }}"
                                                                {{ in_array($cat, old('categorias_adicionales', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="cat_{{ $cat }}">{{ $cat }}</label>
                                                        </div>
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
                                                    <label class="form-label fw-semibold text-dark">
                                                        <i class="bi bi-bell me-1"></i>Categorías a Monitorear para Alertas
                                                    </label>
                                                    <div class="row" id="checkboxes_monitoreadas">
                                                        {{-- Se llena dinámicamente con JavaScript --}}
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="bi bi-info-circle me-1"></i>Solo se generarán alertas de vencimiento para las categorías seleccionadas
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Número Documento</label>
                                                <input type="text" name="documento_numero"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    value="{{ old('documento_numero') }}">
                                            </div>

                                            {{-- Fecha de expedición de la licencia (única para todo el documento) --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Fecha de Expedición</label>
                                                <input type="date" name="documento_fecha_emision" id="documento_fecha_emision"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    value="{{ old('documento_fecha_emision') }}">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>Fecha de expedición de la licencia
                                                </small>
                                            </div>

                                            {{-- Vencimiento de la categoría principal --}}
                                            <div class="col-12" id="vencimiento_principal">
                                                <div class="border rounded-3 p-3 bg-light">
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
                                            <div class="col-12" id="fechas_adicionales_container"></div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Entidad Emisora</label>
                                                <input type="text" name="entidad_emisora"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    placeholder="Ej. Secretaría de Tránsito"
                                                    value="{{ old('entidad_emisora') }}">
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- BOTONES --}}
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-universal px-4 rounded-3">
                                <i class="bi bi-check-circle me-2"></i>Crear Conductor
                            </button>

                            <a href="{{ route('conductores.index') }}" class="btn btn-outline-danger ms-2 rounded-3">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

{{-- Scripts de Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Mostrar/ocultar sección de categorías según tipo de documento
    function toggleSeccionCategorias() {
        const tipoDoc = document.getElementById('documento_tipo').value;
        const seccionCat = document.getElementById('seccion_categorias');
        const seccionCatAd = document.getElementById('seccion_categorias_adicionales');
        const vencimientoPrincipal = document.getElementById('vencimiento_principal');

        if (tipoDoc === 'Licencia Conducción') {
            seccionCat.style.display = 'block';
            seccionCatAd.style.display = 'block';
            vencimientoPrincipal.style.display = 'block';
        } else {
            seccionCat.style.display = 'none';
            seccionCatAd.style.display = 'none';
            vencimientoPrincipal.style.display = 'none';
        }
    }

    // Actualizar label de categoría principal
    function actualizarLabelCategoriaPrincipal() {
        const catPrincipal = document.getElementById('categoria_licencia');
        const label = document.getElementById('label_categoria_principal');
        if (catPrincipal && label) {
            label.textContent = catPrincipal.value || '';
        }
    }

    // Agregar/quitar campos de vencimiento para categorías adicionales
    function actualizarVencimientosAdicionales() {
        const container = document.getElementById('fechas_adicionales_container');
        const catPrincipal = document.getElementById('categoria_licencia').value;
        const checkboxes = document.querySelectorAll('.categoria-adicional:checked');

        container.innerHTML = '';

        checkboxes.forEach(function(checkbox) {
            const cat = checkbox.value;
            // No mostrar si es igual a la categoría principal
            if (cat === catPrincipal) return;

            const html = `
                <div class="border rounded-3 p-3 mt-2 bg-white" id="venc_cat_${cat}">
                    <label class="form-label fw-semibold text-info">
                        <i class="bi bi-calendar-check me-1"></i>
                        Vencimiento Categoría
                        <span class="badge bg-info ms-1">${cat}</span>
                    </label>
                    <input type="date" name="fechas_categoria[${cat}][fecha_vencimiento]"
                        class="form-control rounded-3 border-info-subtle">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    // Actualizar checkboxes de categorías monitoreadas
    function actualizarCategoriasMonitoreadas() {
        const container = document.getElementById('checkboxes_monitoreadas');
        const catPrincipal = document.getElementById('categoria_licencia').value;
        const categoriasAdicionales = Array.from(document.querySelectorAll('.categoria-adicional:checked')).map(cb => cb.value);

        // Todas las categorías seleccionadas (principal + adicionales)
        const todasCategorias = [];
        if (catPrincipal) todasCategorias.push(catPrincipal);
        categoriasAdicionales.forEach(cat => {
            if (cat !== catPrincipal && !todasCategorias.includes(cat)) {
                todasCategorias.push(cat);
            }
        });

        container.innerHTML = '';

        if (todasCategorias.length === 0) {
            container.innerHTML = '<div class="col-12"><small class="text-muted">Seleccione primero una categoría principal</small></div>';
            return;
        }

        todasCategorias.forEach(function(cat) {
            const isPrincipal = cat === catPrincipal;
            const html = `
                <div class="col-4">
                    <div class="form-check">
                        <input class="form-check-input categoria-monitoreada" type="checkbox"
                            name="categorias_monitoreadas[]" value="${cat}" id="mon_${cat}"
                            ${isPrincipal ? 'checked' : ''}>
                        <label class="form-check-label small ${isPrincipal ? 'fw-bold' : ''}" for="mon_${cat}">
                            ${cat} ${isPrincipal ? '(principal)' : ''}
                        </label>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    // Event listeners
    document.getElementById('documento_tipo').addEventListener('change', toggleSeccionCategorias);

    document.getElementById('categoria_licencia').addEventListener('change', function() {
        actualizarLabelCategoriaPrincipal();
        actualizarVencimientosAdicionales();
        actualizarCategoriasMonitoreadas();
    });

    // Deshabilitar categoría principal si está seleccionada como adicional y actualizar vencimientos
    document.querySelectorAll('.categoria-adicional').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const catPrincipal = document.getElementById('categoria_licencia');
            if (this.checked && catPrincipal.value === this.value) {
                this.checked = false;
                alert('Esta categoría ya está seleccionada como principal');
                return;
            }
            actualizarVencimientosAdicionales();
            actualizarCategoriasMonitoreadas();
        });
    });

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        toggleSeccionCategorias();
        actualizarLabelCategoriaPrincipal();
        actualizarVencimientosAdicionales();
        actualizarCategoriasMonitoreadas();
    });

    $(document).ready(function() {
        // Inicializar Select2 en el selector de vehículos
        $('#select-vehiculo').select2({
            theme: 'bootstrap-5',
            placeholder: '🔍 Buscar vehículo por placa, marca, modelo o propietario...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron vehículos";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            templateResult: formatVehiculo,
            templateSelection: formatVehiculoSelection
        });

        // Formato personalizado para los resultados del dropdown
        function formatVehiculo(vehiculo) {
            if (!vehiculo.id) {
                return vehiculo.text;
            }

            var $vehiculo = $(
                '<div class="d-flex align-items-center py-2">' +
                '<div class="me-3">' +
                '<i class="bi bi-car-front-fill fs-4" style="color:#5B8238;"></i>' +
                '</div>' +
                '<div class="flex-grow-1">' +
                '<div class="fw-bold">' + $(vehiculo.element).data('placa') + '</div>' +
                '<small class="text-muted">' +
                $(vehiculo.element).data('marca') + ' ' +
                $(vehiculo.element).data('modelo') + ' - ' +
                $(vehiculo.element).data('color') +
                '</small><br>' +
                '<small class="text-muted">' +
                '<i class="bi bi-person-fill me-1"></i>' +
                $(vehiculo.element).data('propietario') +
                '</small>' +
                '</div>' +
                '</div>'
            );

            return $vehiculo;
        }

        // Formato para la selección (cuando ya está seleccionado)
        function formatVehiculoSelection(vehiculo) {
            if (!vehiculo.id) {
                return vehiculo.text;
            }

            var placa = $(vehiculo.element).data('placa') || '';
            var marca = $(vehiculo.element).data('marca') || '';
            var modelo = $(vehiculo.element).data('modelo') || '';

            return placa + ' - ' + marca + ' ' + modelo;
        }
    });
</script>

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