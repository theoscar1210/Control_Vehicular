{{-- MODAL PARA AGREGAR DOCUMENTO --}}
<div class="modal fade" id="modalAgregarDocumento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color:#5B8238;color:white;">
                <h5 class="modal-title">
                    <i class="fa-solid fa-file-circle-plus me-2"></i>
                    Agregar Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAgregarDocumento" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" id="modal_vehiculo_id" name="vehiculo_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo Documento *</label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                <option value="">Seleccione</option>
                                <option value="SOAT">SOAT</option>
                                <option value="Tecnomecanica">Tecnomecánica</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número *</label>
                            <input type="text" class="form-control" name="numero_documento" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Entidad Emisora</label>
                            <input type="text" class="form-control" name="entidad_emisora">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Emisión *</label>
                            <input type="date" class="form-control" name="fecha_emision" required>
                            <small class="text-muted">Vence automáticamente en 1 año</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="nota" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="btn"
                        style="background-color:#5B8238;color:white;">
                        <i class="fa-solid fa-save me-1"></i> Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>