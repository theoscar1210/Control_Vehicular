/**
 * conductor-form.js
 * Lógica del formulario de creación/asignación de conductor:
 * - Mostrar/ocultar sección de categorías según tipo de documento
 * - Gestión dinámica de fechas de vencimiento por categoría
 * - Checkboxes de categorías monitoreadas
 * - Inicialización de Select2 para selector de vehículo
 */

function toggleSeccionCategorias() {
    const tipoDoc             = document.getElementById('documento_tipo').value;
    const seccionCat          = document.getElementById('seccion_categorias');
    const seccionCatAd        = document.getElementById('seccion_categorias_adicionales');
    const vencimientoPrincipal = document.getElementById('vencimiento_principal');

    const esLicencia = tipoDoc === 'LICENCIA CONDUCCION';
    seccionCat.style.display           = esLicencia ? 'block' : 'none';
    seccionCatAd.style.display         = esLicencia ? 'block' : 'none';
    vencimientoPrincipal.style.display = esLicencia ? 'block' : 'none';
}

function actualizarLabelCategoriaPrincipal() {
    const catPrincipal = document.getElementById('categoria_licencia');
    const label        = document.getElementById('label_categoria_principal');
    if (catPrincipal && label) {
        label.textContent = catPrincipal.value || '';
    }
}

function actualizarVencimientosAdicionales() {
    const container    = document.getElementById('fechas_adicionales_container');
    const catPrincipal = document.getElementById('categoria_licencia').value;
    const checkboxes   = document.querySelectorAll('.categoria-adicional:checked');

    container.innerHTML = '';

    checkboxes.forEach(function (checkbox) {
        const cat = checkbox.value;
        if (cat === catPrincipal) return;

        const html =
            '<div class="border rounded-3 p-3 mb-2 bg-white" id="venc_cat_' + cat + '">' +
            '<label class="form-label fw-semibold text-info">' +
            '<i class="bi bi-calendar-check me-1"></i>' +
            'Vencimiento Categoría ' +
            '<span class="badge bg-info ms-1">' + cat + '</span>' +
            '</label>' +
            '<input type="date" name="fechas_categoria[' + cat + '][fecha_vencimiento]"' +
            ' class="form-control rounded-3 border-info-subtle">' +
            '</div>';
        container.insertAdjacentHTML('beforeend', html);
    });
}

function actualizarCategoriasMonitoreadas() {
    const container            = document.getElementById('checkboxes_monitoreadas');
    const catPrincipal         = document.getElementById('categoria_licencia').value;
    const categoriasAdicionales = Array.from(
        document.querySelectorAll('.categoria-adicional:checked')
    ).map(function (cb) { return cb.value; });

    const todasCategorias = [];
    if (catPrincipal) todasCategorias.push(catPrincipal);
    categoriasAdicionales.forEach(function (cat) {
        if (cat !== catPrincipal && !todasCategorias.includes(cat)) {
            todasCategorias.push(cat);
        }
    });

    container.innerHTML = '';

    if (todasCategorias.length === 0) {
        container.innerHTML =
            '<div class="col-12"><small class="text-muted">Seleccione primero una categoría principal</small></div>';
        return;
    }

    todasCategorias.forEach(function (cat) {
        const isPrincipal = cat === catPrincipal;
        const html =
            '<div class="form-check">' +
            '<input class="form-check-input categoria-monitoreada" type="checkbox"' +
            ' name="categorias_monitoreadas[]" value="' + cat + '" id="mon_' + cat + '"' +
            (isPrincipal ? ' checked' : '') + '>' +
            '<label class="form-check-label ' + (isPrincipal ? 'fw-bold' : '') + '" for="mon_' + cat + '">' +
            cat + (isPrincipal ? ' (principal)' : '') +
            '</label>' +
            '</div>';
        container.insertAdjacentHTML('beforeend', html);
    });
}

document.getElementById('documento_tipo').addEventListener('change', toggleSeccionCategorias);

document.getElementById('categoria_licencia').addEventListener('change', function () {
    actualizarLabelCategoriaPrincipal();
    actualizarVencimientosAdicionales();
    actualizarCategoriasMonitoreadas();
});

document.querySelectorAll('.categoria-adicional').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const catPrincipal = document.getElementById('categoria_licencia');
        if (this.checked && catPrincipal.value === this.value) {
            this.checked = false;
            alert('Esta categoría ya está seleccionada como principal');
            return;
        }
        actualizarVencimientosAdicionales();
        actualizarCategoriasMonitoreadas();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    toggleSeccionCategorias();
    actualizarLabelCategoriaPrincipal();
    actualizarVencimientosAdicionales();
    actualizarCategoriasMonitoreadas();
});

// Select2 para selector de vehículo (requiere jQuery y Select2)
$(document).ready(function () {
    function formatVehiculo(vehiculo) {
        if (!vehiculo.id) return vehiculo.text;
        return $(
            '<div class="d-flex align-items-center py-2">' +
            '<div class="me-3"><i class="bi bi-car-front-fill fs-4" style="color:#5B8238;"></i></div>' +
            '<div class="flex-grow-1">' +
            '<div class="fw-bold">' + $(vehiculo.element).data('placa') + '</div>' +
            '<small class="text-muted">' +
            $(vehiculo.element).data('marca') + ' ' +
            $(vehiculo.element).data('modelo') + ' - ' +
            $(vehiculo.element).data('color') +
            '</small><br>' +
            '<small class="text-muted"><i class="bi bi-person-fill me-1"></i>' +
            $(vehiculo.element).data('propietario') + '</small>' +
            '</div></div>'
        );
    }

    function formatVehiculoSelection(vehiculo) {
        if (!vehiculo.id) return vehiculo.text;
        var placa  = $(vehiculo.element).data('placa')  || '';
        var marca  = $(vehiculo.element).data('marca')  || '';
        var modelo = $(vehiculo.element).data('modelo') || '';
        return placa + ' - ' + marca + ' ' + modelo;
    }

    // Select2 para selector único (legacy)
    if ($('#select-vehiculo').length) {
        $('#select-vehiculo').select2({
            theme:    'bootstrap-5',
            placeholder: '🔍 Buscar vehículo por placa, marca, modelo o propietario...',
            allowClear: true,
            width:    '100%',
            language: {
                noResults:  function () { return 'No se encontraron vehículos'; },
                searching:  function () { return 'Buscando...'; }
            },
            templateResult:    formatVehiculo,
            templateSelection: formatVehiculoSelection
        });
    }

    // Select2 múltiple para asignación de varios vehículos
    if ($('#select-vehiculos').length) {
        $('#select-vehiculos').select2({
            theme:       'bootstrap-5',
            placeholder: '🔍 Buscar por placa, marca o propietario...',
            allowClear:  true,
            width:       '100%',
            language: {
                noResults: function () { return 'No se encontraron vehículos'; },
                searching: function () { return 'Buscando...'; }
            },
            templateResult: formatVehiculo,
            templateSelection: function (vehiculo) {
                if (!vehiculo.id) return vehiculo.text;
                var placa = $(vehiculo.element).data('placa') || vehiculo.text;
                return $('<span><i class="bi bi-car-front-fill me-1" style="color:#5B8238;"></i>' + placa + '</span>');
            }
        });
    }
});
