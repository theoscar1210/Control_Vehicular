@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;


$vehiculoId = request()->query('vehiculo');

@endphp


@extends('layouts.app')

@section('title', 'Nuevo Vehículo')

@section('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .border-success-thick {
        border: 2px solid #198754 !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-...tu_hash..."
    crossorigin="anonymous">
</script>

@endsection

@section('content')
<br><br><br>
{{-- Pasar la condición como atributo --}}
<div id="propietario-section"
    data-aviso="{{ $propietario && !request()->query('vehiculo') ? '1' : '' }}"
    style="display:none;">
</div>

<div class="container mt-4">

    <!-- Breadcrumb y botón nuevo -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vehiculos.index', ['clasificacion' => $clasificacion ?? 'EMPLEADO']) }}"><i class="fa-solid fa-car"></i> Vehículos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo Registro</li>
            </ol>
        </nav>

        @if($propietario || request()->query('vehiculo'))
        <a href="{{ route('vehiculos.create', ['clasificacion' => $clasificacion ?? 'EMPLEADO']) }}" class="btn btn-universal">
            <i class="fa-solid fa-rotate-left me-2"></i>Nuevo Registro
        </a>
        @endif
    </div>

    <h4>Registro de Vehículo</h4>
    <p>Complete la información del vehículo y de los documentos requeridos.</p>
    {{-- Mensajes de éxito --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Mensajes de error --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Errores de validación --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Indicador de progreso --}}
    @if($propietario || request()->query('vehiculo'))
    <div class="progress-wrapper shadow-sm p-3 mb-4 rounded-3 border">

        <div class="d-flex align-items-center mb-2">
            <div class="icon-modern me-3">
                <i class="fa-solid fa-list-check"></i>
            </div>

            <div>
                <strong class="fs-6">Progreso del registro</strong>
            </div>
        </div>

        {{-- Barra de progreso mejorada --}}
        <div class="progress modern-progress mb-2" style="height: 20px;">
            @php
            $vehiculoId = request()->query('vehiculo');
            $progreso = 0;

            if ($propietario) { $progreso = 33; }

            if ($vehiculoId) {
            $progreso = 66;
            $vehiculo = \App\Models\Vehiculo::find($vehiculoId);

            if ($vehiculo) {
            $tieneSoat = $vehiculo->documentos()
            ->activos()
            ->where('tipo_documento', 'SOAT')
            ->exists();

            $tieneTecno = $vehiculo->documentos()
            ->activos()
            ->where('tipo_documento', 'Tecnomecanica')
            ->exists();

            // Si el vehículo está exento de tecnomecánica (nuevo), solo requiere SOAT
            $requiereTecno = $vehiculo->requiereTecnomecanica();

            if ($requiereTecno) {
            // Vehículo normal: requiere SOAT + Tecnomecánica
            if ($tieneSoat && $tieneTecno) {
            $progreso = 100;
            }
            } else {
            // Vehículo nuevo/exento: solo requiere SOAT
            if ($tieneSoat) {
            $progreso = 100;
            }
            }
            }
            }
            @endphp

            <div class="progress-bar modern-progress-bar"
                role="progressbar"
                style="width: {{ $progreso }}%">
                {{ $progreso }}%
            </div>
        </div>

        <small class="text-muted">
            @if($progreso === 100)
            @if(isset($requiereTecno) && !$requiereTecno)
            ✓ Propietario creado | ✓ Vehículo creado | ✓ SOAT registrado (Exento de Tecnomecánica)
            @else
            ✓ Propietario creado | ✓ Vehículo creado | ✓ Documentos registrados
            @endif
            @elseif($vehiculoId)
            ✓ Propietario creado | ✓ Vehículo creado | Registra documentos
            @elseif($propietario)
            ✓ Propietario creado | Registra vehículo
            @else
            Inicia creando un propietario
            @endif
        </small>

    </div>
    @endif
    @if($vehiculo)
    <div class="alert alert-info">
        <strong>Vehículo:</strong> {{ $vehiculo->placa }} <br>
        <strong>Propietario:</strong>
        {{ $vehiculo->propietario->nombre }}
        {{ $vehiculo->propietario->apellido }}
    </div>
    @endif


    <div class="row gy-4 mt-3">

        {{-- 1. FORMULARIO PROPIETARIO --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm {{ $propietario ? 'border-success-thick' : '' }}">
                <div class="card-header bg-header text-white d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-user me-2"></i>1. Registrar Propietario</span>
                    @if($propietario)
                    <span class="badge bg-light text-success">
                        <i class="fa-solid fa-check"></i> Completado
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Formulario oculto para búsqueda de propietario (GET) --}}
                    <form action="{{ route('vehiculos.create') }}" method="GET" id="form-buscar-propietario"></form>

                    @php
                        $propEncontrado = isset($propietarioBuscado) && $propietarioBuscado;
                    @endphp

                    @if($propietario)
                    {{-- CASO 1: Propietario ya seleccionado/creado --}}
                    <div class="alert alert-success mb-3">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        <strong>Propietario:</strong><br>
                        {{ $propietario->nombre }} {{ $propietario->apellido }} - {{ $propietario->tipo_doc }}: {{ $propietario->identificacion }}
                    </div>

                    @elseif($propEncontrado)
                    {{-- CASO 2: Propietario encontrado en búsqueda - Solo mostrar datos y botón usar --}}
                    <div class="alert alert-success mb-0">
                        <i class="fa-solid fa-user-check me-2"></i>
                        <strong>Propietario encontrado:</strong>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Tipo Documento</label>
                            <input type="text" class="form-control" value="{{ $propietarioBuscado->tipo_doc }}" disabled>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Identificación</label>
                            <input type="text" class="form-control" value="{{ $propietarioBuscado->identificacion }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" value="{{ $propietarioBuscado->nombre }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" value="{{ $propietarioBuscado->apellido }}" disabled>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="fa-solid fa-car me-1"></i>
                            Vehículos registrados: {{ $propietarioBuscado->vehiculos()->count() }}
                        </small>
                        <div>
                            <a href="{{ route('vehiculos.create') }}" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fa-solid fa-xmark me-1"></i>Cancelar
                            </a>
                            <form action="{{ route('propietarios.usar-existente') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id_propietario" value="{{ $propietarioBuscado->id_propietario }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-arrow-right me-1"></i>Continuar con este propietario
                                </button>
                            </form>
                        </div>
                    </div>

                    @else
                    {{-- CASO 3: No hay propietario - Mostrar formulario de búsqueda/creación --}}

                    @if(isset($identificacionBuscada) && $identificacionBuscada)
                    <div class="alert alert-warning mb-3">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        No se encontró propietario con identificación <strong>{{ $identificacionBuscada }}</strong>.
                        Complete los datos para crear uno nuevo.
                    </div>
                    @endif

                    <form action="{{ route('propietarios.store') }}" method="POST" id="form-propietario" class="form-con-loader">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_doc" class="form-select @error('tipo_doc') is-invalid @enderror" required>
                                    <option value="CC" {{ old('tipo_doc') == 'CC' ? 'selected' : '' }}>CC</option>
                                    <option value="NIT" {{ old('tipo_doc') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                </select>
                                @error('tipo_doc')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="identificacion"
                                        class="form-control @error('identificacion') is-invalid @enderror"
                                        value="{{ $identificacionBuscada ?? old('identificacion') }}"
                                        placeholder="Digite cédula o NIT" required>
                                    <input type="hidden" form="form-buscar-propietario" name="buscar_identificacion" id="input-buscar-id">
                                    <button type="button" class="btn btn-outline-primary"
                                        onclick="document.getElementById('input-buscar-id').value = document.querySelector('input[name=identificacion]').value; document.getElementById('form-buscar-propietario').submit();"
                                        title="Buscar propietario existente">
                                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                    </button>
                                </div>
                                @error('identificacion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}" required>
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" name="apellido"
                                    class="form-control @error('apellido') is-invalid @enderror"
                                    value="{{ old('apellido') }}" required>
                                @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-universal" data-loading-text="Creando...">
                                <i class="fa-solid fa-user-plus me-2"></i>Crear Propietario
                            </button>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Busque primero si el propietario ya existe. Si no, complete los datos.
                            </p>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>



        {{-- 2. FORMULARIOS DE DOCUMENTOS  --}}



        {{-- 2. FORMULARIO VEHÍCULO --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm {{ request()->query('vehiculo') ? 'border-success-thick' : '' }}">
                <div class="card-header bg-header text-white d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-car me-2"></i>2. Registrar Vehículo</span>
                    @if(request()->query('vehiculo'))
                    <span class="badge bg-light text-success">
                        <i class="fa-solid fa-check"></i> Completado
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$propietario)
                    <div class="alert alert-custom" role="alert">
                        <i class="fa-solid fa-lock me-2"></i>
                        Debes crear primero el propietario para poder registrar el vehículo.
                    </div>
                    @endif

                    <form action="{{ route('vehiculos.store') }}" method="POST" class="form-con-loader">
                        @csrf
                        <input type="hidden" name="clasificacion" value="{{ $clasificacion ?? 'EMPLEADO' }}">
                        @if($propietario)
                        <input type="hidden" name="id_propietario" value="{{ $propietario->id_propietario }}">
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Placa <span class="text-danger">*</span></label>
                                <input type="text" name="placa"
                                    class="form-control text-uppercase @error('placa') is-invalid @enderror"
                                    value="{{ old('placa') }}"
                                    {{ $propietario ? '' : 'disabled' }}>
                                @error('placa')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-select @error('tipo') is-invalid @enderror"
                                    {{ $propietario ? '' : 'disabled' }}>
                                    <option value="">Seleccionar</option>
                                    <option value="Carro" {{ old('tipo') == 'Carro' ? 'selected' : '' }}>Carro</option>
                                    <option value="Moto" {{ old('tipo') == 'Moto' ? 'selected' : '' }}>Moto</option>
                                </select>
                                @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marca <span class="text-danger">*</span></label>
                                <input type="text" name="marca"
                                    class="form-control @error('marca') is-invalid @enderror"
                                    value="{{ old('marca') }}"
                                    {{ $propietario ? '' : 'disabled' }}>
                                @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo <span class="text-danger">*</span></label>
                                <input type="number" name="modelo" min="1900" max="2099"
                                    class="form-control @error('modelo') is-invalid @enderror"
                                    value="{{ old('modelo') }}"
                                    {{ $propietario ? '' : 'disabled' }}>
                                @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <input type="text" name="color"
                                    class="form-control @error('color') is-invalid @enderror"
                                    value="{{ old('color') }}"
                                    {{ $propietario ? '' : 'disabled' }}>
                                @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-universal"
                                {{ $propietario ? '' : 'disabled' }}
                                data-loading-text="Creando...">
                                <i class="fa-solid fa-car-side me-2"></i>Crear Vehículo
                            </button>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fa-solid fa-user me-1"></i>
                                Propietario actual:
                                @if($propietario)
                                <strong>{{ $propietario->nombre }} {{ $propietario->apellido }}</strong>
                                @else
                                <em>(ninguno)</em>
                                @endif
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        {{-- 2. FORMULARIO LICENCIA DE TRÁNSITO --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-header text-white">
                    <i class="fa-solid fa-id-card me-2"></i>2. Licencia de Tránsito
                </div>
                <div class="card-body">




                    @if(!$vehiculoId)
                    <div class="alert alert-custom" role="alert">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Registra primero el vehículo para agregar la licencia.
                    </div>
                    @else
                    <form action="{{ route('vehiculos.documentos.store', $vehiculoId) }}"
                        method="POST"
                        class="form-con-loader"
                        id="form-licencia">

                        @csrf

                        {{-- Identificación --}}
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <input type="hidden" name="tipo_documento" value="Tarjeta Propiedad">

                        <div class="row g-3">

                            {{-- Número --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Número Licencia <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="numero_documento"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    value="{{ old('numero_documento') }}"
                                    required>

                                @error('numero_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Entidad --}}
                            <div class="col-md-6">
                                <label class="form-label">Entidad Emisora</label>
                                <input type="text"
                                    name="entidad_emisora"
                                    class="form-control @error('entidad_emisora') is-invalid @enderror"
                                    value="{{ old('entidad_emisora') }}"
                                    placeholder="Ej: Secretaría de Tránsito">

                                @error('entidad_emisora')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha expedición --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Fecha de Expedición <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    name="fecha_emision"
                                    class="form-control @error('fecha_emision') is-invalid @enderror"
                                    value="{{ old('fecha_emision') }}"
                                    max="{{ now()->toDateString() }}"
                                    required>

                                @error('fecha_emision')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fecha matrícula --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Fecha de Matrícula <span class="text-danger">*</span>
                                    <i class="fa-solid fa-circle-info text-primary ms-1"
                                        data-bs-toggle="tooltip"
                                        title="Importante para calcular cuándo vence la primera Tecnomecánica"></i>
                                </label>
                                <input type="date"
                                    name="fecha_matricula"
                                    id="fecha_matricula_input"
                                    class="form-control @error('fecha_matricula') is-invalid @enderror"
                                    value="{{ old('fecha_matricula') }}"
                                    max="{{ now()->toDateString() }}"
                                    required>

                                @error('fecha_matricula')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    <i class="fa-solid fa-lightbulb text-warning me-1"></i>
                                    Esta fecha determina cuándo vence la primera Tecnomecánica
                                    (Carros: 5 años, Motos: 2 años).
                                </small>
                            </div>

                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="btn btn-universal"
                                data-loading-text="Guardando...">
                                <i class="fa-solid fa-save me-2"></i>Guardar Licencia
                            </button>
                        </div>

                    </form>
                    @endif
                </div>
            </div>
        </div>


        {{-- 3. FORMULARIO DOCUMENTO SOAT --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-header text-white">
                    <i class="fa-solid fa-shield-halved me-2"></i>3. Documento SOAT
                </div>
                <div class="card-body">

                    @if(!$vehiculoId)

                    <div class="alert alert-custom" role="alert">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Registra primero el vehículo para agregar documentos.
                    </div>
                    @else
                    <form action="{{ route('vehiculos.documentos.store', $vehiculoId) }}" method="POST" class="form-con-loader" id="form-soat">
                        @csrf
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <input type="hidden" name="tipo_documento" value="SOAT">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    value="{{ old('numero_documento') }}" required>
                                @error('numero_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Entidad Emisora</label>
                                <input type="text" name="entidad_emisora"
                                    class="form-control @error('entidad_emisora') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'SOAT' ? old('entidad_emisora') : '' }}"
                                    placeholder="Ej: Seguros del Estado">
                                @error('entidad_emisora')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Vigencia <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_emision" id="fecha_emision_soat"
                                    class="form-control @error('fecha_emision') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'SOAT' ? old('fecha_emision') : '' }}" required>
                                <small class="text-muted">Fecha en la que inicia la vigencia </small>
                                @error('fecha_emision')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Fecha Vencimiento
                                    <i class="fa-solid fa-wand-magic-sparkles text-primary ms-1"
                                        title="Se calcula automáticamente"></i>
                                </label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento_soat"
                                    class="form-control " readonly style="pointer-events:none; background-color:#e8f0e9 ;"
                                    value="{{ old('fecha_vencimiento') }}">
                                <input type="hidden" name="estado" id="estado_soat" value="{{ old('estado', $computedEstado ?? '') }}">
                                <small class="text-muted">Se calcula automáticamente (+1 año)</small>
                                @error('fecha_vencimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-universal" data-loading-text="Guardando...">
                                <i class="fa-solid fa-save me-2"></i>Guardar SOAT
                            </button>

                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. FORMULARIO DOCUMENTO TECNOMECÁNICA --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-header ">
                    <i class="fa-solid fa-screwdriver-wrench me-2"></i>4. Documento Tecnomecánica
                </div>
                <div class="card-body">
                    @if(!$vehiculoId)
                    <div class="alert alert-custom " role="alert">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Registra primero el vehículo para agregar documentos.
                    </div>
                    @else
                    @php
                    $vehiculoActual = \App\Models\Vehiculo::find($vehiculoId);
                    $fechaMatricula = $vehiculoActual?->fecha_matricula;
                    $tipoVehiculo = $vehiculoActual?->tipo ?? 'Carro';
                    $requiereTecno = $vehiculoActual?->requiereTecnomecanica() ?? true;
                    $fechaPrimeraRevision = $vehiculoActual?->fechaPrimeraTecnomecanica();
                    $anosPrimeraRevision = $tipoVehiculo === 'Moto' ? 2 : 5;
                    @endphp

                    {{-- VEHÍCULO NUEVO: Exención por tiempo - BLOQUEAR FORMULARIO --}}
                    @if($fechaMatricula && !$requiereTecno)
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="fa-solid fa-shield-check me-1"></i> EXENTO
                                </span>
                            </div>
                            <div>
                                <h6 class="alert-heading mb-1">
                                    <i class="fa-solid fa-car me-1"></i>
                                    Vehículo "Nuevo" (Exención por tiempo)
                                </h6>
                                <p class="mb-0 small">
                                    Este {{ $tipoVehiculo === 'Moto' ? 'motocicleta' : 'vehículo' }} no requiere Tecnomecánica hasta el
                                    <strong>{{ $fechaPrimeraRevision->format('d/m/Y') }}</strong>
                                    ({{ $anosPrimeraRevision }} años desde la matrícula).
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center py-4">
                        <i class="fa-solid fa-clock fa-3x text-success mb-3" style="opacity: 0.5;"></i>
                        <p class="text-muted mb-2">
                            <strong>Días restantes para primera revisión:</strong>
                        </p>
                        <h3 class="text-success">
                            {{ (int) now()->diffInDays($fechaPrimeraRevision) }} días
                        </h3>
                        <p class="text-muted small">
                            Fecha de matrícula: {{ $fechaMatricula->format('d/m/Y') }}
                        </p>
                    </div>

                    {{-- VEHÍCULO QUE YA REQUIERE TECNOMECÁNICA --}}
                    @else
                    @if($fechaMatricula)
                    <div class="alert alert-warning mb-3">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <strong>Revisión requerida:</strong> El vehículo ya superó los {{ $anosPrimeraRevision }} años desde su matrícula.
                        La tecnomecánica se renueva anualmente.
                    </div>
                    @else
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Registra primero la Licencia de Tránsito con la fecha de matrícula
                        para calcular correctamente el vencimiento de la tecnomecánica.
                        <br><small class="text-muted">Regla: Carros nuevos (5 años), Motos nuevas (2 años), luego renovación anual.</small>
                    </div>
                    @endif

                    <form action="{{ route('vehiculos.documentos.store', $vehiculoId) }}" method="POST" class="form-con-loader" id="form-tecno">
                        @csrf
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <input type="hidden" name="tipo_documento" value="Tecnomecanica">
                        {{-- Datos para JavaScript --}}
                        <input type="hidden" id="vehiculo_fecha_matricula" value="{{ $fechaMatricula?->format('Y-m-d') ?? '' }}">
                        <input type="hidden" id="vehiculo_tipo" value="{{ $tipoVehiculo }}">
                        <input type="hidden" id="vehiculo_requiere_tecno" value="{{ $requiereTecno ? '1' : '0' }}">
                        <input type="hidden" id="vehiculo_fecha_primera_revision" value="{{ $fechaPrimeraRevision?->format('Y-m-d') ?? '' }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    value="{{ old('numero_documento') }}" required>
                                @error('numero_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Centro de Revisión</label>
                                <input type="text" name="entidad_emisora"
                                    class="form-control @error('entidad_emisora') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'Tecnomecanica' ? old('entidad_emisora') : '' }}"
                                    placeholder="Ej: CDA Fontibón">
                                @error('entidad_emisora')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Expedicíon <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_emision" id="fecha_emision_tecno"
                                    class="form-control @error('fecha_emision') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'Tecnomecanica' ? old('fecha_emision') : '' }}" required>
                                @error('fecha_emision')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Fecha Vencimiento
                                    <i class="fa-solid fa-wand-magic-sparkles text-primary ms-1"
                                        title="Se calcula automáticamente"></i>
                                </label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento_tecno"
                                    class="form-control @error('fecha_vencimiento') is-invalid @enderror" readonly style="pointer-events:none; background-color:#e8f0e9;"
                                    value="{{ session('fecha_venc_tecnomecanica') ?? old('fecha_vencimiento') }}">
                                <small class="text-muted" id="tecno_vencimiento_info">
                                    Se calcula automáticamente (+1 año desde emisión)
                                </small>
                                @error('fecha_vencimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-universal" data-loading-text="Guardando...">
                                <i class="fa-solid fa-save me-2"></i>Guardar Tecnomecánica
                            </button>

                        </div>
                    </form>
                    @endif {{-- Fin de @else (vehículo que requiere tecnomecánica) --}}
                    @endif {{-- Fin de @if(!$vehiculoId) --}}
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Footer --}}
<footer class="text-center mt-5 mb-3 text-muted small">
    © 2025 Club Campestre Altos del Chicalá. Todos los derechos reservados.
</footer>
@endsection
@if(session('success'))
<div data-flash-success="{{ session('success') }}" style="display:none"></div>
@endif

@if(session('error'))
<div data-flash-error="{{ session('error') }}" style="display:none"></div>
@endif

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* --------------------------------------------------------------------
         * 1. Inicializar tooltips de Bootstrap
         * ------------------------------------------------------------------ */
        const tooltipTriggerList = Array.from(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

        /* --------------------------------------------------------------------
         * 3. Scroll automático según parámetros de URL
         * ------------------------------------------------------------------ */
        const url = new URL(window.location.href);

        if (url.searchParams.get('propietario')) {
            setTimeout(() => {
                const card = document.querySelector('.col-12.col-lg-6:nth-child(2) .card');
                card?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 300);
        }

        if (url.searchParams.get('vehiculo')) {
            setTimeout(() => {
                const card = document.querySelector('.col-12.col-lg-6:nth-child(3) .card');
                card?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 300);
        }


        /* --------------------------------------------------------------------
         * 4. Mayúsculas automáticas en placa
         * ------------------------------------------------------------------ */
        const placaInput = document.querySelector('input[name="placa"]');
        if (placaInput) {
            placaInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }


        /* --------------------------------------------------------------------
         * 5. Loader en formularios (evita doble submit)
         * ------------------------------------------------------------------ */
        const forms = document.querySelectorAll(".form-con-loader");

        forms.forEach(form => {
            form.addEventListener("submit", function(e) {
                const btn = form.querySelector("button[type='submit']");
                if (!btn) return;

                // Si el botón ya está deshabilitado, bloquear el envío
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }

                btn.disabled = true;
                const loadingText = btn.getAttribute("data-loading-text") || "Procesando...";

                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                ${loadingText}
            `;
            });
        });

        /* --------------------------------------------------------------------
         * 7. Toast de éxito (flash message generado desde Laravel)
         * ------------------------------------------------------------------ */
        const flashSuccess = document.querySelector('[data-flash-success]');
        if (flashSuccess) {
            showBootstrapToast(flashSuccess.dataset.flashSuccess, 'success');
        }

        function showBootstrapToast(message, type = 'info') {
            const container = document.getElementById('toast-container') || createToastContainer();

            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'primary'} border-0`;
            toastEl.role = 'alert';
            toastEl.ariaLive = 'assertive';
            toastEl.ariaAtomic = 'true';
            toastEl.style.minWidth = '220px';

            toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

            container.appendChild(toastEl);

            const bsToast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            bsToast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        function createToastContainer() {
            const div = document.createElement('div');
            div.id = 'toast-container';
            div.className = 'position-fixed top-0 end-0 p-3';
            div.style.zIndex = 1080;
            document.body.appendChild(div);
            return div;
        }


        /* --------------------------------------------------------------------
         * 8. Confirmación antes de abandonar página
         * ------------------------------------------------------------------ */
        const propietarioSection = document.getElementById('propietario-section');
        if (propietarioSection && propietarioSection.dataset.aviso === '1') {
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = "Has creado un propietario pero no un vehículo. ¿Deseas salir?";
                return e.returnValue;
            });
        }

        /* --------------------------------------------------------------------
         * 9. Cálculo automático de fecha de vencimiento SOAT (+1 año)
         * ------------------------------------------------------------------ */
        const fechaEmisionSoat = document.getElementById('fecha_emision_soat');
        const fechaVencimientoSoat = document.getElementById('fecha_vencimiento_soat');

        if (fechaEmisionSoat && fechaVencimientoSoat) {
            fechaEmisionSoat.addEventListener('change', function() {
                if (this.value) {
                    const fecha = new Date(this.value);
                    fecha.setFullYear(fecha.getFullYear() + 1);
                    fechaVencimientoSoat.value = fecha.toISOString().split('T')[0];
                } else {
                    fechaVencimientoSoat.value = '';
                }
            });
        }

        /* --------------------------------------------------------------------
         * 10. Cálculo automático de fecha de vencimiento TECNOMECÁNICA
         *     Regla:
         *     - Vehículos nuevos (Carro): Primera revisión a los 5 años
         *     - Motos nuevas: Primera revisión a los 2 años
         *     - Después de primera revisión: Renovación anual
         * ------------------------------------------------------------------ */
        const fechaEmisionTecno = document.getElementById('fecha_emision_tecno');
        const fechaVencimientoTecno = document.getElementById('fecha_vencimiento_tecno');
        const fechaMatriculaHidden = document.getElementById('vehiculo_fecha_matricula');
        const tipoVehiculoHidden = document.getElementById('vehiculo_tipo');
        const requiereTecnoHidden = document.getElementById('vehiculo_requiere_tecno');
        const fechaPrimeraRevisionHidden = document.getElementById('vehiculo_fecha_primera_revision');
        const tecnoInfoText = document.getElementById('tecno_vencimiento_info');

        if (fechaEmisionTecno && fechaVencimientoTecno) {
            fechaEmisionTecno.addEventListener('change', function() {
                if (!this.value) {
                    fechaVencimientoTecno.value = '';
                    return;
                }

                const fechaEmision = new Date(this.value);
                const fechaMatricula = fechaMatriculaHidden?.value ? new Date(fechaMatriculaHidden.value) : null;
                const tipoVehiculo = tipoVehiculoHidden?.value || 'Carro';
                const requiereTecno = requiereTecnoHidden?.value === '1';
                const fechaPrimeraRevision = fechaPrimeraRevisionHidden?.value ? new Date(fechaPrimeraRevisionHidden.value) : null;

                let fechaVencimiento;

                // Si no tiene fecha de matrícula o ya requiere tecnomecánica, +1 año
                if (!fechaMatricula || requiereTecno) {
                    fechaVencimiento = new Date(fechaEmision);
                    fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 1);
                    if (tecnoInfoText) {
                        tecnoInfoText.textContent = 'Se calcula automáticamente (+1 año desde emisión)';
                    }
                } else {
                    // Vehículo nuevo: usar fecha de primera revisión
                    fechaVencimiento = fechaPrimeraRevision;
                    if (tecnoInfoText) {
                        const anos = tipoVehiculo === 'Moto' ? 2 : 5;
                        tecnoInfoText.textContent = `Vence en la fecha de primera revisión (${anos} años desde matrícula)`;
                    }
                }

                if (fechaVencimiento) {
                    fechaVencimientoTecno.value = fechaVencimiento.toISOString().split('T')[0];
                }
            });

            // Disparar cálculo inicial si ya hay fecha de emisión
            if (fechaEmisionTecno.value) {
                fechaEmisionTecno.dispatchEvent(new Event('change'));
            }
        }

    });
</script>
@endsection