@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background-color:#198754;">
                    <h4 class="mb-0 text-center">Editar Conductor</h4>
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
                            @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('conductores.update', $conductor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Información básica --}}
                        <h5 class="text-success mb-3"><i class="bi bi-person-lines-fill"></i> Información del Conductor</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $conductor->nombre) }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" name="apellido" value="{{ old('apellido', $conductor->apellido) }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipo Documento</label>
                                <select name="tipo_doc" class="form-select border-success-subtle">
                                    <option value="CC" {{ old('tipo_doc', $conductor->tipo_doc) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="CE" {{ old('tipo_doc', $conductor->tipo_doc) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Identificación</label>
                                <input type="text" name="identificacion" value="{{ old('identificacion', $conductor->identificacion) }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $conductor->telefono) }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Teléfono de Emergencia</label>
                                <input type="text" name="telefono_emergencia" value="{{ old('telefono_emergencia', $conductor->telefono_emergencia) }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="activo" value="1" id="activo" class="form-check-input border-success"
                                        {{ old('activo', $conductor->activo) ? 'checked' : '' }}>
                                    <label for="activo" class="form-check-label fw-semibold">Activo</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Asignar a Vehículo</label>
                                <select name="id_vehiculo" class="form-select border-success-subtle">
                                    <option value="">-- Sin asignar --</option>
                                    @foreach($vehiculos as $v)
                                    <option value="{{ $v->id_vehiculo }}"
                                        {{ (old('id_vehiculo') == $v->id_vehiculo) || ($v->id_conductor == $conductor->id_conductor) ? 'selected' : '' }}>
                                        {{ $v->placa }} — {{ $v->marca }} {{ $v->modelo ?? '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Tabla de documentos --}}
                        <h5 class="text-info mb-3"><i class="bi bi-file-earmark-text"></i> Documentación</h5>
                        <p class="text-muted mb-2">Historial de documentos del conductor</p>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle text-center">
                                <thead class="table-success">
                                    <tr>
                                        <th>Tipo</th>
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
                                        <td>{{ $doc->numero_documento }}</td>
                                        <td>{{ $doc->version }}</td>
                                        <td>{{ optional($doc->fecha_registro)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $doc->estado }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay documentos registrados.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Acción sobre documentación --}}
                        <h6 class="fw-semibold text-secondary mb-3">Acción sobre documentación</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Acción</label>
                            <select name="documento_action" class="form-select border-success-subtle">
                                <option value="none" {{ old('documento_action') == 'none' ? 'selected' : '' }}>No hacer nada</option>
                                <option value="update_existing" {{ old('documento_action') == 'update_existing' ? 'selected' : '' }}>Actualizar documento existente (solo metadata)</option>
                                <option value="create_version" {{ old('documento_action') == 'create_version' ? 'selected' : '' }}>Crear nueva versión (sin archivo)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Documento existente</label>
                                <select name="documento_id" class="form-select border-success-subtle">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($documentos as $doc)
                                    <option value="{{ $doc->id_doc_conductor }}" {{ old('documento_id') == $doc->id_doc_conductor ? 'selected' : '' }}>
                                        {{ $doc->tipo_documento }} — v{{ $doc->version }} — {{ $doc->numero_documento ?? '—' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipo</label>
                                <input name="documento_tipo" value="{{ old('documento_tipo') }}" class="form-control border-success-subtle" placeholder="Ej: Licencia Conducción">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Número Documento</label>
                                <input name="documento_numero" value="{{ old('documento_numero') }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Fecha Emisión</label>
                                <input type="date" name="documento_fecha_emision" value="{{ old('documento_fecha_emision') }}" class="form-control border-success-subtle">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Fecha Vencimiento</label>
                                <input type="date" name="documento_fecha_vencimiento" value="{{ old('documento_fecha_vencimiento') }}" class="form-control border-success-subtle">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save"></i> Actualizar
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