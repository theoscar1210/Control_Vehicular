@php
    $claseBorde = $infoCat['clase'];
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-{{ $claseBorde }} text-white">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="fa-solid fa-sync-alt me-2"></i>
                    Refrendar Categoría {{ $categoria }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('conductores.documentos.renovar-categoria', $conductor->id_conductor) }}" method="POST">
                @csrf
                <input type="hidden" name="documento_id" value="{{ $doc->id_doc_conductor }}">
                <input type="hidden" name="categoria" value="{{ $categoria }}">

                <div class="modal-body">
                    {{-- Info del conductor --}}
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <strong>{{ $conductor->nombre }} {{ $conductor->apellido }}</strong>
                        <br>
                        <small>{{ $conductor->tipo_doc }} {{ $conductor->identificacion }}</small>
                    </div>

                    {{-- Info de la categoría actual --}}
                    <div class="alert alert-{{ $claseBorde }} mb-3">
                        <i class="fa-solid fa-exclamation-triangle me-1"></i>
                        <strong>Categoría a refrendar: {{ $categoria }}</strong>
                        <br>
                        <small>
                            @if($infoCat['fecha'])
                            Vencimiento actual: {{ $infoCat['fecha']->format('d/m/Y') }}
                            @if($infoCat['dias'] < 0)
                            <span class="text-danger">(Vencido hace {{ abs($infoCat['dias']) }} días)</span>
                            @else
                            <span>({{ $infoCat['dias'] }} días restantes)</span>
                            @endif
                            @else
                            Sin fecha de vencimiento registrada
                            @endif
                        </small>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        Nueva fecha de vencimiento
                    </h6>

                    {{-- Nueva fecha de vencimiento --}}
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

                    {{-- Checkbox para agregar a monitoreadas --}}
                    @php
                        $esMonitoreada = in_array($categoria, $doc->getCategoriasAMonitorear());
                    @endphp
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="agregar_monitoreo" id="agregar_monitoreo_{{ $modalId }}" {{ $esMonitoreada ? 'checked' : '' }}>
                        <label class="form-check-label" for="agregar_monitoreo_{{ $modalId }}">
                            <i class="fa-solid fa-bell me-1"></i>
                            Monitorear esta categoría para alertas de vencimiento
                        </label>
                    </div>

                    <div class="alert alert-warning small mb-0">
                        <i class="fa-solid fa-history me-1"></i>
                        <strong>Trazabilidad:</strong> Se creará una nueva versión del documento para mantener el historial de refrendaciones.
                        La versión anterior quedará registrada para auditorías.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-{{ $claseBorde }}">
                        <i class="fa-solid fa-save me-1"></i> Refrendar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
