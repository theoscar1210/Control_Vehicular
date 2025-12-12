@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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
                                            <label class="form-label fw-semibold">Asignar a Vehículo</label>

                                            <select name="id_vehiculo" class="form-select rounded-3 border-success-subtle">
                                                <option value="">--- Sin asignar ---</option>
                                                @foreach($vehiculos as $veh)
                                                <option value="{{ $veh->id_vehiculo }}"
                                                    {{ old('id_vehiculo') == $veh->id_vehiculo ? 'selected' : '' }}>
                                                    {{ $veh->placa }} — {{ $veh->marca }} — {{ $veh->modelo ?? '' }}
                                                </option>
                                                @endforeach
                                            </select>
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
@endsection