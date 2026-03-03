/**
 * conductor-edit.js
 * Muestra u oculta la sección de categoría de licencia en el formulario de edición
 * según el tipo de documento seleccionado.
 */
document.addEventListener('DOMContentLoaded', function () {
    const tipoDocSelect    = document.getElementById('documento_tipo_edit');
    const seccionCategoria = document.getElementById('seccion_categoria_edit');

    if (!tipoDocSelect || !seccionCategoria) return;

    function toggleCategoriaLicencia() {
        seccionCategoria.style.display =
            tipoDocSelect.value === 'LICENCIA CONDUCCION' ? 'block' : 'none';
    }

    tipoDocSelect.addEventListener('change', toggleCategoriaLicencia);
    toggleCategoriaLicencia();
});
