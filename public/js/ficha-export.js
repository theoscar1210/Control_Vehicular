/**
 * ficha-export.js
 * Exportación a PDF en las fichas detalladas de vehículo y conductor.
 *
 * La URL del PDF se lee desde el botón con data-pdf-url:
 *   <button data-export-format="pdf" data-pdf-url="{{ route(...) }}">Exportar PDF</button>
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-export-format="pdf"][data-pdf-url]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            window.open(this.dataset.pdfUrl, '_blank');
        });
    });
});
