/**
 * ui-common.js
 * Inicialización compartida: tooltips de Bootstrap y cierre automático de alertas.
 * Usado en: vehiculos/index, vehiculos/trashed, conductores/index, conductores/trashed.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar tooltips
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function (el) { new bootstrap.Tooltip(el); });

    // Auto-cerrar alertas flash después de 5 segundos
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            new bootstrap.Alert(alert).close();
        });
    }, 5000);
});
