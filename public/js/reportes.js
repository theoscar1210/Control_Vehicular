/**
 * reportes.js
 * Función de exportación de reportes (Excel / PDF) para todas las páginas de reportes.
 *
 * Las URLs de exportación se leen desde data-attrs del elemento #export-config:
 *   <div id="export-config"
 *        data-url-excel="/reportes/export/X/excel"
 *        data-url-pdf="/reportes/export/X/pdf"
 *        data-use-form="false">   ← "true" si los params vienen de un form#filtrosForm
 *   </div>
 *
 * Los botones de exportación usan data-export-format="excel|pdf"  (sin onclick inline).
 */
document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('export-config');
    if (!config) return;

    const urlExcel  = config.dataset.urlExcel  || '';
    const urlPdf    = config.dataset.urlPdf    || '';
    const useForm   = config.dataset.useForm === 'true';

    function exportarReporte(formato) {
        let baseUrl = formato === 'excel' ? urlExcel : urlPdf;
        let params  = '';

        if (useForm) {
            const form = document.getElementById('filtrosForm');
            if (form) {
                params = new URLSearchParams(new FormData(form)).toString();
            }
        } else {
            params = window.location.search.replace(/^\?/, '');
        }

        const separator = baseUrl.includes('?') ? '&' : '?';
        window.open(params ? baseUrl + separator + params : baseUrl, '_blank');
    }

    // Vincular botones con data-export-format (reemplaza onclick inline)
    document.querySelectorAll('[data-export-format]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            exportarReporte(this.dataset.exportFormat);
        });
    });
});
