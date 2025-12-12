@php
$navbarEspecial = true;
$ocultarNavbar = true;
$sinPadding = true;
@endphp


@extends('layouts.app')

@section('title', 'Nuevo Vehículo')

@section('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .btn-reset-flow {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .btn-reset-flow:hover {
        animation: none;
        transform: scale(1.1);
    }

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
                <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}"><i class="fa-solid fa-car"></i> Vehículos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo Registro</li>
            </ol>
        </nav>

        @if($propietario || request()->query('vehiculo'))
        <a href="{{ route('vehiculos.create') }}" class="btn btn-universal">
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

            if ($vehiculo
            && $vehiculo->documentos()
            ->activos()
            ->whereIn('tipo_documento', ['SOAT','Tecnomecanica'])
            ->count() === 2) {
            $progreso = 100;
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
            ✓ Propietario creado | ✓ Vehículo creado | ✓ Documentos registrados
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
                    @if($propietario)
                    <div class="alert alert-success mb-3">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        <strong>Propietario creado:</strong><br>
                        {{ $propietario->nombre }} {{ $propietario->apellido }} - {{ $propietario->tipo_doc }}: {{ $propietario->identificacion }}
                    </div>
                    @endif

                    <form action="{{ route('propietarios.store') }}" method="POST" id="form-propietario" class="form-con-loader">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}"
                                    {{ $propietario ? 'disabled' : '' }} required>
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" name="apellido"
                                    class="form-control @error('apellido') is-invalid @enderror"
                                    value="{{ old('apellido') }}"
                                    {{ $propietario ? 'disabled' : '' }} required>
                                @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_doc"
                                    class="form-select @error('tipo_doc') is-invalid @enderror"
                                    {{ $propietario ? 'disabled' : '' }} required>
                                    <option value="CC" {{ old('tipo_doc') == 'CC' ? 'selected' : '' }}>CC</option>
                                    <option value="NIT" {{ old('tipo_doc') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                </select>
                                @error('tipo_doc')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Identificación <span class="text-danger">*</span></label>
                                <input type="text" name="identificacion"
                                    class="form-control @error('identificacion') is-invalid @enderror"
                                    value="{{ old('identificacion') }}"
                                    {{ $propietario ? 'disabled' : '' }} required>
                                @error('identificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            @if(!$propietario)
                            <button type="submit" class="btn btn-universal" data-loading-text="Creando...">
                                <i class="fa-solid fa-user-plus me-2"></i>Crear Propietario
                            </button>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Al crear el propietario, se habilitará el formulario del vehículo.
                            </p>
                            @else
                            {{-- Aquí estaba el botón de "Crear Otro Propietario"
                                pero se ha eliminado sin afectar la estructura ni la lógica --}}
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

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

        {{-- 3. FORMULARIO DOCUMENTO SOAT --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-header text-white">
                    <i class="fa-solid fa-shield-halved me-2"></i>3. Documento SOAT
                </div>
                <div class="card-body">
                    @php $vehiculoId = request()->query('vehiculo') ?? null; @endphp

                    @if(!$vehiculoId)
                    <div class="alert alert-custom" role="alert">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Registra primero el vehículo para agregar documentos.
                    </div>
                    @else
                    <form action="{{ route('documentos.store', $vehiculoId) }}" method="POST" class="form-con-loader" id="form-soat">
                        @csrf
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <input type="hidden" name="tipo_documento" value="SOAT">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'SOAT' ? old('numero_documento') : '' }}" required>
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
                                <label class="form-label">Fecha Emisión <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_emision" id="fecha_emision_soat"
                                    class="form-control @error('fecha_emision') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'SOAT' ? old('fecha_emision') : '' }}" required>
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
                                    value="{{ session('fecha_venc_soat') ?? old('fecha_vencimiento') }}">
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
                    <form action="{{ route('documentos.store', $vehiculoId) }}" method="POST" class="form-con-loader" id="form-tecno">
                        @csrf
                        <input type="hidden" name="id_vehiculo" value="{{ $vehiculoId }}">
                        <input type="hidden" name="tipo_documento" value="Tecnomecanica">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento"
                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                    value="{{ old('tipo_documento') == 'Tecnomecanica' ? old('numero_documento') : '' }}" required>
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
                                <label class="form-label">Fecha Emisión <span class="text-danger">*</span></label>
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
                                    value="{{ session('fecha_venc_tecnomecanica') ?? session('fecha_venc_tecnomecanica') ?? old('fecha_vencimiento') }}">
                                <small class="text-muted">Se calcula automáticamente (+1 año)</small>
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
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
<br>
{{-- Botón flotante para reiniciar (solo visible después de crear propietario) --}}
@if($propietario || request()->query('vehiculo'))
<a href="{{ route('vehiculos.create') }}"
    class="btn btn-universal  btn-reset-flow"
    data-bs-toggle="tooltip"
    data-bs-placement="left"
    title="Reiniciar y crear nuevo registro completo">
    <i class="fa-solid fa-plus me-2"></i>Nuevo Registro
</a>
@endif
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
                if (!btn || btn.disabled) return;

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

    });
</script>
@endsection