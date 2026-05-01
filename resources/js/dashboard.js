document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;
    var body = document.body;
    var sidebar = document.querySelector('[data-dashboard-sidebar]');
    var toggleButton = document.querySelector('[data-dashboard-toggle]');
    var closeButton = document.querySelector('[data-dashboard-close]');
    var collapseButton = document.querySelector('[data-dashboard-collapse]');
    var collapseIcon = document.querySelector('[data-dashboard-collapse-icon]');
    var backdrop = document.querySelector('[data-dashboard-backdrop]');
    var resizer = document.querySelector('[data-dashboard-resizer]');
    var sidebarWidthStorageKey = 'frangy-control-dashboard-sidebar-width';
    var sidebarCollapsedStorageKey = 'frangy-control-dashboard-sidebar-collapsed';
    var defaultSidebarWidth = 320;
    var minSidebarWidth = 260;
    var maxSidebarWidth = 420;
    var activePointerId = null;
    var isResizingSidebar = false;

    function isDesktopViewport() {
        return window.matchMedia('(min-width: 992px)').matches;
    }

    function clampSidebarWidth(value) {
        return Math.min(maxSidebarWidth, Math.max(minSidebarWidth, Math.round(value)));
    }

    function readSidebarWidth() {
        try {
            var storedWidth = parseInt(localStorage.getItem(sidebarWidthStorageKey), 10);

            if (!Number.isNaN(storedWidth)) {
                return clampSidebarWidth(storedWidth);
            }
        } catch (error) {
            return defaultSidebarWidth;
        }

        return defaultSidebarWidth;
    }

    function applySidebarWidth(value, persist) {
        var nextWidth = clampSidebarWidth(value);

        root.style.setProperty('--dashboard-sidebar-width', nextWidth + 'px');

        if (resizer) {
            resizer.setAttribute('aria-valuenow', String(nextWidth));
        }

        if (!persist) {
            return;
        }

        try {
            localStorage.setItem(sidebarWidthStorageKey, String(nextWidth));
        } catch (error) {
            return;
        }
    }

    function readSidebarCollapsed() {
        try {
            return localStorage.getItem(sidebarCollapsedStorageKey) === 'true';
        } catch (error) {
            return false;
        }
    }

    function updateSidebarCollapseControls(isCollapsed) {
        if (!collapseButton) {
            return;
        }

        collapseButton.setAttribute('aria-pressed', isCollapsed ? 'true' : 'false');
        collapseButton.setAttribute('aria-label', isCollapsed ? 'Expandir navegación lateral' : 'Contraer navegación lateral');

        if (collapseIcon) {
            collapseIcon.className = isCollapsed ? 'fas fa-angle-right' : 'fas fa-angle-left';
        }
    }

    function setSidebarCollapsed(isCollapsed, persist) {
        root.classList.toggle('dashboard-sidebar-collapsed', isCollapsed);
        updateSidebarCollapseControls(isCollapsed);

        if (isCollapsed) {
            stopSidebarResize();
        }

        if (!persist) {
            return;
        }

        try {
            localStorage.setItem(sidebarCollapsedStorageKey, isCollapsed ? 'true' : 'false');
        } catch (error) {
            return;
        }
    }

    function stopSidebarResize(event) {
        if (!isResizingSidebar) {
            return;
        }

        if (event && activePointerId !== null && event.pointerId !== activePointerId) {
            return;
        }

        isResizingSidebar = false;
        body.classList.remove('dashboard-sidebar-resizing');

        if (resizer && activePointerId !== null && resizer.hasPointerCapture(activePointerId)) {
            resizer.releasePointerCapture(activePointerId);
        }

        activePointerId = null;
        applySidebarWidth(parseInt(getComputedStyle(root).getPropertyValue('--dashboard-sidebar-width'), 10) || defaultSidebarWidth, true);
    }

    function closeSidebar() {
        root.classList.remove('dashboard-sidebar-open');

        if (toggleButton) {
            toggleButton.setAttribute('aria-expanded', 'false');
        }
    }

    function openSidebar() {
        root.classList.add('dashboard-sidebar-open');

        if (toggleButton) {
            toggleButton.setAttribute('aria-expanded', 'true');
        }
    }

    setSidebarCollapsed(readSidebarCollapsed(), false);

    if (collapseButton) {
        collapseButton.addEventListener('click', function () {
            if (!isDesktopViewport()) {
                return;
            }

            setSidebarCollapsed(!root.classList.contains('dashboard-sidebar-collapsed'), true);
        });
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

    if (sidebar && resizer) {
        applySidebarWidth(readSidebarWidth(), false);

        resizer.addEventListener('pointerdown', function (event) {
            if (!isDesktopViewport() || root.classList.contains('dashboard-sidebar-collapsed')) {
                return;
            }

            activePointerId = event.pointerId;
            isResizingSidebar = true;
            body.classList.add('dashboard-sidebar-resizing');
            resizer.setPointerCapture(event.pointerId);
            event.preventDefault();
        });

        window.addEventListener('pointermove', function (event) {
            if (!isResizingSidebar) {
                return;
            }

            if (activePointerId !== null && event.pointerId !== activePointerId) {
                return;
            }

            applySidebarWidth(event.clientX, false);
        });

        window.addEventListener('pointerup', stopSidebarResize);
        window.addEventListener('pointercancel', stopSidebarResize);

        resizer.addEventListener('dblclick', function () {
            applySidebarWidth(defaultSidebarWidth, true);
        });

        resizer.addEventListener('keydown', function (event) {
            if (!isDesktopViewport() || root.classList.contains('dashboard-sidebar-collapsed')) {
                return;
            }

            var currentWidth = parseInt(getComputedStyle(root).getPropertyValue('--dashboard-sidebar-width'), 10) || defaultSidebarWidth;

            if (event.key === 'ArrowLeft') {
                applySidebarWidth(currentWidth - 16, true);
                event.preventDefault();
            }

            if (event.key === 'ArrowRight') {
                applySidebarWidth(currentWidth + 16, true);
                event.preventDefault();
            }

            if (event.key === 'Home') {
                applySidebarWidth(minSidebarWidth, true);
                event.preventDefault();
            }

            if (event.key === 'End') {
                applySidebarWidth(maxSidebarWidth, true);
                event.preventDefault();
            }
        });

        window.addEventListener('resize', function () {
            if (!isDesktopViewport()) {
                stopSidebarResize();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    document.querySelectorAll('.dashboard-sidebar__link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
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

    document.querySelectorAll('[data-dismiss="alert"]').forEach(function (button) {
        button.addEventListener('click', function () {
            var alert = button.closest('.alert');

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
