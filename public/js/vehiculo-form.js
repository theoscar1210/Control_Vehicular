/**
 * vehiculo-form.js
 * Lógica del formulario de creación/edición de vehículo:
 * - Tooltips Bootstrap
 * - Scroll automático según parámetros de URL
 * - Mayúsculas automáticas en placa
 * - Loader en formularios (previene doble submit)
 * - Toast de éxito para mensajes flash
 * - Confirmación antes de abandonar página
 * - Cálculo automático de fechas de vencimiento (SOAT, Tecnomecánica)
 */
document.addEventListener('DOMContentLoaded', function () {

    /* -----------------------------------------------------------------------
     * 1. Tooltips Bootstrap
     * --------------------------------------------------------------------- */
    Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .forEach(function (el) { new bootstrap.Tooltip(el); });

    /* -----------------------------------------------------------------------
     * 3. Scroll automático según parámetros de URL
     * --------------------------------------------------------------------- */
    var url = new URL(window.location.href);

    if (url.searchParams.get('propietario')) {
        setTimeout(function () {
            var card = document.querySelector('.col-12.col-lg-6:nth-child(2) .card');
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }

    if (url.searchParams.get('vehiculo')) {
        setTimeout(function () {
            var card = document.querySelector('.col-12.col-lg-6:nth-child(3) .card');
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }

    /* -----------------------------------------------------------------------
     * 4. Mayúsculas automáticas en placa
     * --------------------------------------------------------------------- */
    var placaInput = document.querySelector('input[name="placa"]');
    if (placaInput) {
        placaInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }

    /* -----------------------------------------------------------------------
     * 5. Loader en formularios (evita doble submit)
     * --------------------------------------------------------------------- */
    document.querySelectorAll('.form-con-loader').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var btn = form.querySelector("button[type='submit']");
            if (!btn) return;
            if (btn.disabled) { e.preventDefault(); return; }

            btn.disabled = true;
            var loadingText = btn.getAttribute('data-loading-text') || 'Procesando...';
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>' +
                loadingText;
        });
    });

    /* -----------------------------------------------------------------------
     * 7. Toast de éxito (flash message desde Laravel)
     * --------------------------------------------------------------------- */
    function createToastContainer() {
        var div = document.createElement('div');
        div.id = 'toast-container';
        div.className = 'position-fixed top-0 end-0 p-3';
        div.style.zIndex = 1080;
        document.body.appendChild(div);
        return div;
    }

    function showBootstrapToast(message, type) {
        type = type || 'info';
        var container = document.getElementById('toast-container') || createToastContainer();
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-bg-' +
            (type === 'success' ? 'success' : 'primary') + ' border-0';
        toastEl.role = 'alert';
        toastEl.style.minWidth = '220px';
        toastEl.innerHTML =
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto"' +
            ' data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>';
        container.appendChild(toastEl);
        var bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
    }

    var flashSuccess = document.querySelector('[data-flash-success]');
    if (flashSuccess) showBootstrapToast(flashSuccess.dataset.flashSuccess, 'success');

    /* -----------------------------------------------------------------------
     * 8. Confirmación antes de abandonar página
     * --------------------------------------------------------------------- */
    var propietarioSection = document.getElementById('propietario-section');
    if (propietarioSection && propietarioSection.dataset.aviso === '1') {
        var enviandoFormulario = false;

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                enviandoFormulario = true;
            });
        });

        window.addEventListener('beforeunload', function (e) {
            if (enviandoFormulario) return;
            e.preventDefault();
            e.returnValue = 'Has creado un propietario pero no un vehículo. ¿Deseas salir?';
            return e.returnValue;
        });
    }

    /* -----------------------------------------------------------------------
     * 9. Cálculo automático de fecha de vencimiento SOAT (+1 año)
     * --------------------------------------------------------------------- */
    var fechaEmisionSoat      = document.getElementById('fecha_emision_soat');
    var fechaVencimientoSoat  = document.getElementById('fecha_vencimiento_soat');

    if (fechaEmisionSoat && fechaVencimientoSoat) {
        fechaEmisionSoat.addEventListener('change', function () {
            if (this.value) {
                var fecha = new Date(this.value);
                fecha.setFullYear(fecha.getFullYear() + 1);
                fechaVencimientoSoat.value = fecha.toISOString().split('T')[0];
            } else {
                fechaVencimientoSoat.value = '';
            }
        });
    }

    /* -----------------------------------------------------------------------
     * 10. Cálculo automático de fecha de vencimiento Tecnomecánica
     * --------------------------------------------------------------------- */
    var fechaEmisionTecno          = document.getElementById('fecha_emision_tecno');
    var fechaVencimientoTecno      = document.getElementById('fecha_vencimiento_tecno');
    var fechaMatriculaHidden       = document.getElementById('vehiculo_fecha_matricula');
    var tipoVehiculoHidden         = document.getElementById('vehiculo_tipo');
    var requiereTecnoHidden        = document.getElementById('vehiculo_requiere_tecno');
    var fechaPrimeraRevisionHidden = document.getElementById('vehiculo_fecha_primera_revision');
    var tecnoInfoText              = document.getElementById('tecno_vencimiento_info');

    if (fechaEmisionTecno && fechaVencimientoTecno) {
        fechaEmisionTecno.addEventListener('change', function () {
            if (!this.value) {
                fechaVencimientoTecno.value = '';
                return;
            }

            var fechaEmision        = new Date(this.value);
            var fechaMatricula      = fechaMatriculaHidden && fechaMatriculaHidden.value
                                        ? new Date(fechaMatriculaHidden.value) : null;
            var tipoVehiculo        = tipoVehiculoHidden ? tipoVehiculoHidden.value || 'Carro' : 'Carro';
            var requiereTecno       = requiereTecnoHidden && requiereTecnoHidden.value === '1';
            var fechaPrimeraRevision = fechaPrimeraRevisionHidden && fechaPrimeraRevisionHidden.value
                                        ? new Date(fechaPrimeraRevisionHidden.value) : null;

            var fechaVencimiento;

            if (!fechaMatricula || requiereTecno) {
                fechaVencimiento = new Date(fechaEmision);
                fechaVencimiento.setFullYear(fechaVencimiento.getFullYear() + 1);
                if (tecnoInfoText) {
                    tecnoInfoText.textContent = 'Se calcula automáticamente (+1 año desde emisión)';
                }
            } else {
                fechaVencimiento = fechaPrimeraRevision;
                if (tecnoInfoText) {
                    var anos = tipoVehiculo === 'Moto' ? 2 : 5;
                    tecnoInfoText.textContent =
                        'Vence en la fecha de primera revisión (' + anos + ' años desde matrícula)';
                }
            }

            if (fechaVencimiento) {
                fechaVencimientoTecno.value = fechaVencimiento.toISOString().split('T')[0];
            }
        });

        if (fechaEmisionTecno.value) {
            fechaEmisionTecno.dispatchEvent(new Event('change'));
        }
    }
});
