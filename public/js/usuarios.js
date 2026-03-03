/**
 * usuarios.js
 * Gestión de la tabla de usuarios: selección de fila, activación/desactivación,
 * confirmación de eliminación y edición.
 *
 * La URL base se lee desde el atributo data-base-url del contenedor #usuarios-table-container.
 * Ejemplo: <div id="usuarios-table-container" data-base-url="/usuarios">
 */
document.addEventListener('DOMContentLoaded', function () {
    const container       = document.getElementById('usuarios-table-container');
    const baseUrl         = container ? container.dataset.baseUrl : '/usuarios';

    const radios          = document.querySelectorAll('.radio-usuario');
    const btnEditar       = document.getElementById('btnEditar');
    const btnEliminar     = document.getElementById('btnEliminar');
    const btnToggleActivo = document.getElementById('btnToggleActivo');
    const btnToggleTexto  = document.getElementById('btnToggleTexto');
    const seleccionInfo   = document.getElementById('seleccionInfo');
    const formEliminar    = document.getElementById('formEliminar');
    const formToggleActivo = document.getElementById('formToggleActivo');
    const modalEl         = document.getElementById('modalEliminar');
    const nombreUsuarioEl = document.getElementById('nombreUsuarioEliminar');
    const btnConfirmar    = document.getElementById('btnConfirmarEliminar');

    if (!modalEl) return;

    const modalEliminar = new bootstrap.Modal(modalEl);

    let usuarioSeleccionado = null;
    let nombreSeleccionado  = '';
    let usuarioActivo       = false;

    // Seleccionar fila completa al hacer clic
    document.querySelectorAll('.fila-usuario').forEach(function (fila) {
        fila.style.cursor = 'pointer';
        fila.addEventListener('click', function (e) {
            if (e.target.type === 'radio') return;
            const radio = this.querySelector('.radio-usuario');
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    });

    // Manejar cambio en radio buttons
    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (!this.checked) return;

            usuarioSeleccionado = this.value;
            const fila          = this.closest('.fila-usuario');
            nombreSeleccionado  = fila.dataset.nombre;
            usuarioActivo       = fila.dataset.activo === '1';

            btnEditar.disabled       = false;
            btnEliminar.disabled     = false;
            btnToggleActivo.disabled = false;

            if (usuarioActivo) {
                btnToggleTexto.textContent = 'Desactivar';
                btnToggleActivo.classList.replace('btn-success', 'btn-secondary');
            } else {
                btnToggleTexto.textContent = 'Activar';
                btnToggleActivo.classList.replace('btn-secondary', 'btn-success');
            }

            seleccionInfo.innerHTML =
                '<i class="fas fa-user-check text-success me-1"></i>Seleccionado: <strong>' +
                nombreSeleccionado + '</strong>';

            document.querySelectorAll('.fila-usuario').forEach(function (f) {
                f.classList.remove('table-active');
            });
            fila.classList.add('table-active');
        });
    });

    btnEditar.addEventListener('click', function () {
        if (usuarioSeleccionado) {
            window.location.href = baseUrl + '/' + usuarioSeleccionado + '/edit';
        }
    });

    btnToggleActivo.addEventListener('click', function () {
        if (usuarioSeleccionado) {
            formToggleActivo.action = baseUrl + '/' + usuarioSeleccionado + '/toggle-activo';
            formToggleActivo.submit();
        }
    });

    btnEliminar.addEventListener('click', function () {
        if (usuarioSeleccionado) {
            if (nombreUsuarioEl) nombreUsuarioEl.textContent = nombreSeleccionado;
            modalEliminar.show();
        }
    });

    btnConfirmar.addEventListener('click', function () {
        if (usuarioSeleccionado) {
            formEliminar.action = baseUrl + '/' + usuarioSeleccionado;
            formEliminar.submit();
        }
    });
});
