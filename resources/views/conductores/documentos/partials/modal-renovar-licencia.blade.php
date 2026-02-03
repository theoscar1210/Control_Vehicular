@php
    $estado = $doc->estado;
    $claseBorde = $estado === 'VENCIDO' ? 'danger' : 'warning';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-{{ $claseBorde }} text-white">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="fa-solid fa-sync-alt me-2"></i>
                    Renovar Licencia de Conducción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('conductores.documentos.renovar', $conductor->id_conductor) }}" method="POST">
                @csrf
                <input type="hidden" name="documento_id" value="{{ $doc->id_doc_conductor }}">

                <div class="modal-body">
                    {{-- Info del conductor --}}
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <strong>{{ $conductor->nombre }} {{ $conductor->apellido }}</strong>
                        <br>
                        <small>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}</small>
                    </div>

                    {{-- Info del documento actual --}}
                    <div class="alert alert-{{ $claseBorde }} mb-3">
                        <i class="fa-solid fa-exclamation-triangle me-1"></i>
                        <strong>Documento actual:</strong>
                        <br>
                        <small>
                            Categoría: {{ $doc->categoria_licencia ?? 'N/A' }} |
                            Vencimiento: {{ $doc->fecha_vencimiento ? \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                            <br>
                            Versión actual: {{ $doc->version }}
                        </small>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-file-circle-plus me-1"></i>
                        Nueva información de la licencia
                    </h6>

                    {{-- Categoría (mantener la actual o cambiar) --}}
                    <div class="mb-3">
                        <label class="form-label">Categoría de Licencia</label>
                        <select name="categoria_licencia" class="form-select">
                            <option value="{{ $doc->categoria_licencia }}" selected>
                                {{ $doc->categoria_licencia }} (Actual)
                            </option>
                            <optgroup label="Motocicletas">
                                <option value="A1">A1 - Motocicletas hasta 125cc</option>
                                <option value="A2">A2 - Motocicletas más de 125cc</option>
                            </optgroup>
                            <optgroup label="Vehículos Particulares">
                                <option value="B1">B1 - Automóviles, Camperos, Camionetas</option>
                                <option value="B2">B2 - Camiones, Buses</option>
                                <option value="B3">B3 - Vehículos Articulados</option>
                            </optgroup>
                        </select>
                        <small class="text-muted">Puede mantener la categoría actual o seleccionar una diferente</small>
                    </div>

                    {{-- Número de documento --}}
                    <div class="mb-3">
                        <label class="form-label">Número de Licencia <span class="text-danger">*</span></label>
                        <input type="text"
                            name="numero_documento"
                            class="form-control"
                            value="{{ $doc->numero_documento }}"
                            required>
                    </div>

                    {{-- Entidad emisora --}}
                    <div class="mb-3">
                        <label class="form-label">Entidad Emisora</label>
                        <input type="text"
                            name="entidad_emisora"
                            class="form-control"
                            value="{{ $doc->entidad_emisora }}"
                            placeholder="Ej: Secretaría de Tránsito">
                    </div>

                    {{-- Fecha de expedición --}}
                    <div class="mb-3">
                        <label class="form-label">Fecha de Expedición <span class="text-danger">*</span></label>
                        <input type="date"
                            name="fecha_emision"
                            class="form-control"
                            required
                            max="{{ now()->toDateString() }}">
                    </div>

                    {{-- Fecha de vencimiento --}}
                    <div class="mb-3">
                        <label class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                        <input type="date"
                            name="fecha_vencimiento"
                            class="form-control"
                            required
                            min="{{ now()->addDay()->toDateString() }}">
                        <small class="text-muted">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            La vigencia de la licencia depende de la edad del conductor
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-{{ $claseBorde }}">
                        <i class="fa-solid fa-save me-1"></i> Renovar Licencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
