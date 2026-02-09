{{-- MODAL RENOVAR SOAT --}}
<div class="modal fade" id="{{ $modalId }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header text-white" style="background-color:#5B8238;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-arrow-rotate-right me-2"></i>
                    Renovar SOAT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('vehiculos.documentos.update', [$vehiculo->id_vehiculo, $doc->id_doc_vehiculo]) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- INFO VERSIÓN --}}
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Versión actual:</strong> v{{ $doc->version }}<br>
                        <strong>Nueva versión:</strong> v{{ $doc->version + 1 }}
                    </div>

                    <div class="row">
                        {{-- NÚMERO PÓLIZA --}}
                        <div class="col-md-6 mb-3">
                            <label for="numero_soat_{{ $doc->id_doc_vehiculo }}" class="form-label fw-semibold">
                                Número de Póliza <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('numero_documento') is-invalid @enderror"
                                id="numero_soat_{{ $doc->id_doc_vehiculo }}"
                                name="numero_documento"
                                value="{{ old('numero_documento', '') }}"
                                placeholder="Ej: 123456789"
                                required>
                            @error('numero_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ASEGURADORA --}}
                        <div class="col-md-6 mb-3">
                            <label for="aseguradora_{{ $doc->id_doc_vehiculo }}" class="form-label fw-semibold">
                                Aseguradora
                            </label>
                            <input type="text"
                                class="form-control @error('entidad_emisora') is-invalid @enderror"
                                id="aseguradora_{{ $doc->id_doc_vehiculo }}"
                                name="entidad_emisora"
                                value="{{ old('entidad_emisora', '') }}"
                                placeholder="Ej: SURA, Liberty, Mapfre">
                            @error('entidad_emisora')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- FECHA EMISIÓN --}}
                        <div class="col-md-6 mb-3">
                            <label for="fecha_emision_{{ $doc->id_doc_vehiculo }}" class="form-label fw-semibold">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                class="form-control @error('fecha_emision') is-invalid @enderror"
                                id="fecha_emision_{{ $doc->id_doc_vehiculo }}"
                                name="fecha_emision"
                                value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}"
                                required>
                            @error('fecha_emision')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                El vencimiento se calcula automáticamente (+1 año)
                            </small>
                        </div>

                        {{-- FECHA VENCIMIENTO (INFORMATIVA) --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Fecha de Vencimiento
                            </label>
                            <input type="text"
                                class="form-control bg-light"
                                value="Se calcula automáticamente"
                                readonly>
                            <small class="text-muted">
                                <i class="fa-solid fa-calculator me-1"></i>
                                Fecha de emisión + 1 año
                            </small>
                        </div>
                    </div>



                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn text-white" style="background-color:#5B8238;"
                        onclick="this.disabled=true; this.innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span> Renovando...'; this.closest('form').submit();">
                        <i class="fa-solid fa-check-circle me-1"></i> Renovar SOAT
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>