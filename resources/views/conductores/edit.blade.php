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
                                <select name="documento_tipo" id="documento_tipo_edit" class="form-select border-success-subtle">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Licencia Conducción" {{ old('documento_tipo') == 'Licencia Conducción' ? 'selected' : '' }}>Licencia Conducción</option>
                                    {{-- Opciones deshabilitadas para futuras actualizaciones --}}
                                    {{-- <option value="EPS" {{ old('documento_tipo') == 'EPS' ? 'selected' : '' }}>EPS</option> --}}
                                    {{-- <option value="ARL" {{ old('documento_tipo') == 'ARL' ? 'selected' : '' }}>ARL</option> --}}
                                    {{-- <option value="Certificado Médico" {{ old('documento_tipo') == 'Certificado Médico' ? 'selected' : '' }}>Certificado Médico</option> --}}
                                    {{-- <option value="Otro" {{ old('documento_tipo') == 'Otro' ? 'selected' : '' }}>Otro</option> --}}
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="seccion_categoria_edit" style="display: none;">
                                <label class="form-label fw-semibold">Categoría de Licencia</label>
                                <select name="categoria_licencia" id="categoria_licencia_edit" class="form-select border-success-subtle">
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
                                </select>
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
                            <a href="{{ route('conductores.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoDocSelect = document.getElementById('documento_tipo_edit');
        const seccionCategoria = document.getElementById('seccion_categoria_edit');

        function toggleCategoriaLicencia() {
            if (tipoDocSelect.value === 'Licencia Conducción') {
                seccionCategoria.style.display = 'block';
            } else {
                seccionCategoria.style.display = 'none';
            }
        }

        tipoDocSelect.addEventListener('change', toggleCategoriaLicencia);
        toggleCategoriaLicencia(); // Initial check
    });
</script>
@endsection