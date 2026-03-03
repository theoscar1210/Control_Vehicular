/**
 * porteria.js
 * Actualiza el placeholder y el label del buscador según el tipo de búsqueda
 * seleccionado en el formulario de portería.
 */
document.addEventListener('DOMContentLoaded', function () {
    const tipoBusqueda  = document.getElementById('tipoBusqueda');
    const inputBusqueda = document.querySelector('input[name="busqueda"]');
    const labelBusqueda = document.getElementById('labelBusqueda');

    if (!tipoBusqueda || !inputBusqueda || !labelBusqueda) return;

    const placeholders = {
        todo:        'Placa, nombre, cédula...',
        placa:       'Ej: ABC123',
        conductor:   'Nombre o apellido del conductor',
        propietario: 'Nombre o apellido del propietario',
        documento:   'Número de cédula o documento'
    };

    const labels = {
        todo:        'Término de búsqueda:',
        placa:       'Placa del vehículo:',
        conductor:   'Nombre del conductor:',
        propietario: 'Nombre del propietario:',
        documento:   'Número de documento:'
    };

    tipoBusqueda.addEventListener('change', function () {
        inputBusqueda.placeholder = placeholders[this.value] || placeholders.todo;
        labelBusqueda.textContent = labels[this.value]       || labels.todo;
    });
});
