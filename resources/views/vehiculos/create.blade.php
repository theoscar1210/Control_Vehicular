{{-- resources/views/vehiculos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Registro de Vehículo')

@section('head')
<!-- Bootstrap Icons (si no están en el layout) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    /* Estilos específicos para esta vista (basados en el prototipo) */
    .step-progress {
        background: #f1f1f1;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
    }

    .card-ghost {
        background: #f5f5f7;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: none;
        border: none;
    }

    .card-ghost .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #0d6efd;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        margin-right: 12px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
    }

    .form-section-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }

    .register-btn {
        background: #4a7a36;
        border: none;
        padding: 12px 22px;
        border-radius: 28px;
        color: #fff;
        box-shadow: 0 6px 18px rgba(74, 122, 54, 0.15);
    }

    .small-muted {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .breadcrumb-custom {
        background: transparent;
        padding: 0;
        margin-bottom: 0.75rem;
    }

    /* responsive spacing */
    @media (max-width: 767px) {
        .card-ghost {
            padding: .9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb / header -->
    <nav aria-label="breadcrumb" class="breadcrumb-custom">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vehiculos.index') }}">Vehículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Registro</li>
        </ol>
    </nav>
    <br>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Registro de Vehículo</h3>
            <p class="small-muted mb-0">Complete la información del vehículo y de los documentos requeridos</p>
        </div>
    </div>

    {{-- Progreso --}}
    <div class="step-progress mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="small-muted">Progreso del Registro</div>
            <div class="small-muted">0%</div>
        </div>

        <div class="progress" style="height:8px;">
            <div class="progress-bar" role="progressbar" style="width:0%; background:#d9d9d9;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div class="d-flex justify-content-between text-muted mt-3" style="font-size:0.85rem;">
            <div>Información Básica</div>
            <div>Propietario</div>
            <div>Seguro</div>
            <div>Tecnomecanica</div>
            <div>Finalizado</div>
        </div>
    </div>
    <br>
    {{-- Formulario en 2 columnas + 2 filas --}}
    <form action="{{ route('vehiculos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row gx-4 gy-4">

            <!-- Información del Vehículo -->
            <div class="col-12 col-lg-6">
                <div class="card-ghost">
                    <div class="form-section-title mb-3">
                        <div class="card-icon"><i class="bi bi-truck-front-fill"></i></div>
                        <div>
                            <div style="font-weight:700">Información del Vehículo</div>
                            <div class="small-muted">Datos básicos del vehículo</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placa del Vehículo <span class="text-danger">*</span></label>
                            <input type="text" name="placa" value="{{ old('placa') }}" class="form-control" placeholder="ABC123">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select">
                                <option value="">Seleccionar tipo</option>
                                <option value="Carro" {{ old('tipo')=='Carro' ? 'selected' : '' }}>Carro</option>
                                <option value="Moto" {{ old('tipo')=='Moto' ? 'selected' : '' }}>Moto</option>
                                <option value="Camion" {{ old('tipo')=='Camion' ? 'selected' : '' }}>Camión</option>
                                <option value="Otro" {{ old('tipo')=='Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" value="{{ old('marca') }}" class="form-control" placeholder="Toyota, Chevrolet, etc.">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" value="{{ old('modelo') }}" class="form-control" placeholder="Corolla, Spark, etc.">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año <span class="text-danger">*</span></label>
                            <input type="number" min="1900" max="2099" step="1" name="anio" value="{{ old('anio') }}" class="form-control" placeholder="2020">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color <span class="text-danger">*</span></label>
                            <input type="text" name="color" value="{{ old('color') }}" class="form-control" placeholder="Blanco, Negro, etc.">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Propietario -->
            <div class="col-12 col-lg-6">
                <div class="card-ghost">
                    <div class="form-section-title mb-3">
                        <div class="card-icon" style="background:#0dcaf0"><i class="bi bi-person-circle"></i></div>
                        <div>
                            <div style="font-weight:700">Información del Propietario</div>
                            <div class="small-muted">Datos del dueño del vehículo</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="prop_nombre" value="{{ old('prop_nombre') }}" class="form-control" placeholder="Juan Pérez">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="prop_tipo_doc" class="form-select">
                                <option value="">Seleccionar tipo</option>
                                <option value="CC" {{ old('prop_tipo_doc')=='CC' ? 'selected' : '' }}>CC</option>
                                <option value="NIT" {{ old('prop_tipo_doc')=='NIT' ? 'selected' : '' }}>NIT</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                            <input type="text" name="prop_identificacion" value="{{ old('prop_identificacion') }}" class="form-control" placeholder="Digite el número de documento">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="prop_telefono" value="{{ old('prop_telefono') }}" class="form-control" placeholder="310 555 4567">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="prop_direccion" value="{{ old('prop_direccion') }}" class="form-control" placeholder="Calle 123 #45-67">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="prop_email" value="{{ old('prop_email') }}" class="form-control" placeholder="propietario@email.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Seguro -->
            <div class="col-12 col-lg-6">
                <div class="card-ghost">
                    <div class="form-section-title mb-3">
                        <div class="card-icon" style="background:#198754"><i class="bi bi-shield-lock-fill"></i></div>
                        <div>
                            <div style="font-weight:700">Información del Seguro</div>
                            <div class="small-muted">Datos de la póliza (SOAT / Póliza)</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Compañía Aseguradora <span class="text-danger">*</span></label>
                            <select name="aseguradora" class="form-select">
                                <option value="">Seleccionar aseguradora</option>
                                <option value="AXA" {{ old('aseguradora')=='AXA' ? 'selected' : '' }}>AXA</option>
                                <option value="SURA" {{ old('aseguradora')=='SURA' ? 'selected' : '' }}>SURA</option>
                                <option value="LIBERTY" {{ old('aseguradora')=='LIBERTY' ? 'selected' : '' }}>LIBERTY</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Póliza <span class="text-danger">*</span></label>
                            <input type="text" name="numero_poliza" value="{{ old('numero_poliza') }}" class="form-control" placeholder="Ingrese el número de póliza">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio_poliza" value="{{ old('fecha_inicio_poliza') }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vencimiento_poliza" value="{{ old('fecha_vencimiento_poliza') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revisión Técnico-Mecánica -->
            <div class="col-12 col-lg-6">
                <div class="card-ghost">
                    <div class="form-section-title mb-3">
                        <div class="card-icon" style="background:#0d6efd"><i class="bi bi-card-checklist"></i></div>
                        <div>
                            <div style="font-weight:700">Revisión Técnico-Mecánica</div>
                            <div class="small-muted">Datos del certificado técnico</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Centro de Diagnóstico <span class="text-danger">*</span></label>
                            <input type="text" name="cda" value="{{ old('cda') }}" class="form-control" placeholder="Nombre del CDA">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Certificado <span class="text-danger">*</span></label>
                            <input type="text" name="numero_certificado" value="{{ old('numero_certificado') }}" class="form-control" placeholder="Ingrese el número de certificado">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Expedición <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_expedicion" value="{{ old('fecha_expedicion') }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vencimiento_tm" value="{{ old('fecha_vencimiento_tm') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- /row -->

        {{-- Errores globales --}}
        @if ($errors->any())
        <div class="mt-3">
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Botón central --}}
        <div class="d-flex justify-content-center my-5">
            <button class="register-btn" type="submit">
                <i class="bi bi-plus-lg me-2"></i> Registrar Vehículo
            </button>
        </div>

    </form>

    {{-- Footer pequeño --}}
    <div class="text-center text-muted mt-5 mb-3">
        © {{ date('Y') }} Club Campestre Altos del Chicalá. Todos los derechos reservados.
    </div>
</div>
@endsection