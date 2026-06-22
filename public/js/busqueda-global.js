/**
 * busqueda-global.js
 * Autocomplete en navbar para búsqueda de vehículos, conductores y propietarios.
 */
(function () {
    const input      = document.getElementById('busqueda-global-input');
    const dropdown   = document.getElementById('busqueda-global-dropdown');
    const form       = document.getElementById('busqueda-global-form');
    const ajaxUrl    = input ? input.dataset.ajaxUrl : null;
    const resultUrl  = input ? input.dataset.resultUrl : null;

    if (!input || !dropdown) return;

    let timer = null;
    let ultimaQuery = '';

    function limpiarDropdown() {
        dropdown.innerHTML = '';
        dropdown.classList.remove('show');
    }

    function crearItem(icono, color, label, sub, url) {
        const a = document.createElement('a');
        a.href = url;
        a.className = 'dropdown-item py-2 border-bottom';
        a.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="${icono} text-${color} fa-fw"></i>
                <div class="lh-sm overflow-hidden">
                    <div class="fw-semibold text-truncate small">${label}</div>
                    <div class="text-muted" style="font-size:0.75rem">${sub}</div>
                </div>
            </div>`;
        return a;
    }

    function crearEncabezado(texto) {
        const li = document.createElement('div');
        li.className = 'dropdown-header text-uppercase fw-bold small py-1 px-3';
        li.style.fontSize = '0.7rem';
        li.style.letterSpacing = '0.05em';
        li.textContent = texto;
        return li;
    }

    function crearVerTodos(q) {
        const a = document.createElement('a');
        a.href = resultUrl + '?q=' + encodeURIComponent(q);
        a.className = 'dropdown-item text-center py-2 small text-success fw-semibold';
        a.innerHTML = '<i class="fas fa-list me-1"></i>Ver todos los resultados';
        return a;
    }

    function renderDropdown(data, q) {
        limpiarDropdown();

        const total = (data.vehiculos?.length || 0) + (data.conductores?.length || 0) + (data.propietarios?.length || 0);

        if (total === 0) {
            const vacio = document.createElement('div');
            vacio.className = 'dropdown-item text-muted text-center py-3 small';
            vacio.innerHTML = '<i class="fas fa-search me-1"></i>Sin resultados para "' + q + '"';
            dropdown.appendChild(vacio);
            dropdown.classList.add('show');
            return;
        }

        if (data.vehiculos?.length) {
            dropdown.appendChild(crearEncabezado('Vehículos'));
            data.vehiculos.forEach(v => dropdown.appendChild(crearItem('fas fa-car', 'success', v.label, v.sub, v.url)));
        }

        if (data.conductores?.length) {
            dropdown.appendChild(crearEncabezado('Conductores'));
            data.conductores.forEach(c => dropdown.appendChild(crearItem('fas fa-id-card', 'primary', c.label, c.sub, c.url)));
        }

        if (data.propietarios?.length) {
            dropdown.appendChild(crearEncabezado('Propietarios'));
            data.propietarios.forEach(p => dropdown.appendChild(crearItem('fas fa-user-tie', 'secondary', p.label, p.sub, p.url)));
        }

        dropdown.appendChild(crearVerTodos(q));
        dropdown.classList.add('show');
    }

    async function buscar(q) {
        if (q === ultimaQuery) return;
        ultimaQuery = q;

        if (q.length < 2) { limpiarDropdown(); return; }

        try {
            const res  = await fetch(ajaxUrl + '?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (input.value.trim() === q) renderDropdown(data, q);
        } catch (_) {
            limpiarDropdown();
        }
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { limpiarDropdown(); return; }
        timer = setTimeout(() => buscar(q), 280);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { limpiarDropdown(); input.blur(); }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const q = input.value.trim();
        if (q.length < 2) return;
        limpiarDropdown();
        window.location.href = resultUrl + '?q=' + encodeURIComponent(q);
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!form.contains(e.target)) limpiarDropdown();
    });

    // Re-abrir si hay texto y el campo recibe foco de nuevo
    input.addEventListener('focus', () => {
        const q = input.value.trim();
        if (q.length >= 2 && ultimaQuery === q) {
            dropdown.classList.add('show');
        }
    });
})();
