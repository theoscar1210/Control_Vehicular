@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@section('content')
<br><br>
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
                                                <select name="documento_tipo" class="form-select rounded-3 border-success-subtle">
                                                    <option value="Licencia Conducción">Licencia Conducción</option>
                                                    <option value="Certificado Médico">Certificado Médico</option>
                                                    <option value="ARL">ARL</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Número Documento</label>
                                                <input type="text" name="documento_numero"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    value="{{ old('documento_numero') }}">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">F. Emisión</label>
                                                <input type="date" name="documento_fecha_emision"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    value="{{ old('documento_fecha_emision') }}">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">F. Vencimiento</label>
                                                <input type="date" name="documento_fecha_vencimiento"
                                                    class="form-control rounded-3 border-success-subtle"
                                                    value="{{ old('documento_fecha_vencimiento') }}">
                                            </div>

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

                            <a href="{{ url()->previous() }}" class="btn btn-outline-danger ms-2 rounded-3">
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