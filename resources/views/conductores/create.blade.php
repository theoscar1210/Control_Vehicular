@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-15">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background-color: #198754;">
                    <h4 class="mb-0 text-center">Crear Conductor</h4>
                </div>

                <div class="card-body">
                    {{-- Mensaje de éxito --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{-- Errores de validación --}}
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

                        <h5 class="text-success mb-3"><i class="bi bi-person-lines-fill"></i> Información del Conductor</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control border-success-subtle" placeholder="Ingrese el nombre">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" name="apellido" value="{{ old('apellido') }}" class="form-control border-success-subtle" placeholder="Ingrese el apellido">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipo de Documento</label>
                                <select name="tipo_doc" class="form-select border-success-subtle">
                                    <option value="">Seleccionar</option>
                                    <option value="CC" {{ old('tipo_doc') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="CE" {{ old('tipo_doc') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Identificación</label>
                                <input type="text" name="identificacion" value="{{ old('identificacion') }}" class="form-control border-success-subtle" placeholder="Número de identificación">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control border-success-subtle" placeholder="Ej. 3105557890">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Teléfono de Emergencia</label>
                                <input type="text" name="telefono_emergencia" value="{{ old('telefono_emergencia') }}" class="form-control border-success-subtle" placeholder="Ej. 3100000000">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input border-success" type="checkbox" name="activo" value="1" id="activo"
                                        {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="activo">
                                        Activo
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Asignar a Vehículo (opcional)</label>
                                <select name="id_vehiculo" class="form-select border-success-subtle">
                                    <option value="">--- Sin asignar ---</option>
                                    @foreach($vehiculos as $veh)
                                    <option value="{{ $veh->id_vehiculo }}" {{ old('id_vehiculo') == $veh->id_vehiculo ? 'selected' : '' }}>
                                        {{ $veh->placa }} — {{ $veh->marca }} {{ $veh->modelo ?? '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="text-info mb-3"><i class="bi bi-file-earmark-text"></i> Documento (Licencia de Conducción)</h5>

                        <div class="row">
                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label fw-semibold">Tipo de Documento</label>
                                <select name="documento_tipo" class="form-select border-success-subtle">
                                    <option value="Licencia Conducción" {{ old('documento_tipo') == 'Licencia Conducción' ? 'selected' : '' }}>Licencia Conducción</option>
                                    <option value="Certificado Médico" {{ old('documento_tipo') == 'Certificado Médico' ? 'selected' : '' }}>Certificado Médico</option>
                                    <option value="ARL" {{ old('documento_tipo') == 'ARL' ? 'selected' : '' }}>ARL</option>
                                    <option value="Otro" {{ old('documento_tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label fw-semibold">Número Documento</label>
                                <input type="text" name="documento_numero" value="{{ old('documento_numero') }}" class="form-control border-success-subtle" placeholder="Ej. 123456">
                            </div>

                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label fw-semibold">F.Emisión</label>
                                <input type="date" name="documento_fecha_emision" value="{{ old('documento_fecha_emision') }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label fw-semibold">F.Vencimiento</label>
                                <input type="date" name="documento_fecha_vencimiento" value="{{ old('documento_fecha_vencimiento') }}" class="form-control border-success-subtle">
                            </div>


                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Entidad Emisora</label>
                                <input type="text" name="entidad_emisora" value="{{ old('entidad_emisora') }}" class="form-control border-success-subtle" placeholder="Ej. Secretaría de transito">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle"></i> Crear Conductor
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection