/**
 * app-layout.js
 * Sidebar, alertas y auto-logout para el layout principal.
 * Las variables de sesión y rutas se leen desde data-attrs del <body>.
 */

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body    = document.body;
    const isOpen  = sidebar.classList.toggle('open');
    overlay.classList.toggle('active', isOpen);
    body.classList.toggle('sidebar-open', isOpen);
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body    = document.body;
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    body.classList.remove('sidebar-open');
}

document.addEventListener('DOMContentLoaded', function () {
    const sidebar      = document.getElementById('sidebar');
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');

    // Cerrar sidebar al hacer click en enlace (solo móvil/tablet)
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (this.getAttribute('data-bs-toggle') === 'collapse') return;
            if (window.innerWidth <= 991) closeSidebar();
        });
    });

    // Cerrar sidebar con tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // Cerrar sidebar al pasar a escritorio
    let resizeTimeout;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            if (window.innerWidth > 991 && sidebar) closeSidebar();
        }, 100);
    });

    // Marcar enlace activo en sidebar
    const currentPath = window.location.pathname;
    sidebarLinks.forEach(function (link) {
        const href = link.getAttribute('href');
        if (href === currentPath || (href && currentPath.startsWith(href) && href !== '/')) {
            link.classList.add('active');
            const parentCollapse = link.closest('.collapse');
            if (parentCollapse) parentCollapse.classList.add('show');
        }
    });

    // Marcar alerta como leída via fetch
    window.markAlertRead = function (id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        fetch('/alertas/' + id + '/read', {
            method:  'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (!json.ok) return;
            fetch('/alertas/unread-count')
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    const badge = document.querySelector('.badge.bg-danger');
                    if (badge) badge.innerText = d.unread;
                });
            const alertRow = document.getElementById('alert-row-' + id);
            if (alertRow) alertRow.classList.add('text-muted');
        });
    };
});

// Auto-logout por inactividad.
// Lee session lifetime y URL de login desde data-attrs del <body>:
//   data-session-lifetime="120"   (minutos)
//   data-login-url="/login"
(function () {
    const body               = document.body;
    const sessionLifetimeMin = parseInt(body.dataset.sessionLifetime || '120', 10);
    const loginUrl           = body.dataset.loginUrl || '/login';
    const SESSION_MS         = sessionLifetimeMin * 60 * 1000;
    const WARNING_MS         = 60 * 1000;
    let inactivityTimer, warningTimer;

    function resetTimers() {
        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);

        warningTimer = setTimeout(function () {
            alert('Tu sesión se cerrará en 1 minuto por inactividad.');
        }, SESSION_MS - WARNING_MS);

        inactivityTimer = setTimeout(function () {
            window.location.href = loginUrl;
        }, SESSION_MS);
    }

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (evt) {
        document.addEventListener(evt, resetTimers, { passive: true });
    });

    resetTimers();
}());
