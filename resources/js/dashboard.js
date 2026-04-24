document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;
    var toggleButton = document.querySelector('[data-dashboard-toggle]');
    var closeButton = document.querySelector('[data-dashboard-close]');
    var backdrop = document.querySelector('[data-dashboard-backdrop]');

    function closeSidebar() {
        root.classList.remove('dashboard-sidebar-open');
    }

    function openSidebar() {
        root.classList.add('dashboard-sidebar-open');
    }

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            if (root.classList.contains('dashboard-sidebar-open')) {
                closeSidebar();
                return;
            }

            openSidebar();
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', closeSidebar);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    document.querySelectorAll('[data-dashboard-dismiss]').forEach(function (button) {
        button.addEventListener('click', function () {
            var alert = button.closest('[data-dashboard-alert]');

            if (!alert) {
                return;
            }

            alert.remove();
        });
    });

    window.setTimeout(function () {
        document.querySelectorAll('[data-dashboard-alert]').forEach(function (alert) {
            alert.remove();
        });
    }, 6000);

    document.addEventListener('click', function (event) {
        document.querySelectorAll('.dashboard-user-menu[open]').forEach(function (dropdown) {
            if (!dropdown.contains(event.target)) {
                dropdown.removeAttribute('open');
            }
        });
    });
});
