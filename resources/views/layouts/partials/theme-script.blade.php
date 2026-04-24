<script>
    (function () {
        var storageKey = 'frangy-control-theme';
        var root = document.documentElement;
        var themeButtons = Array.prototype.slice.call(document.querySelectorAll('[data-theme-toggle]'));

        function getStoredTheme() {
            try {
                return localStorage.getItem(storageKey);
            } catch (error) {
                return null;
            }
        }

        function prefersDarkMode() {
            return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        }

        function resolveTheme() {
            var storedTheme = getStoredTheme();

            if (storedTheme === 'light' || storedTheme === 'dark') {
                return storedTheme;
            }

            return prefersDarkMode() ? 'dark' : 'light';
        }

        function persistTheme(theme) {
            try {
                localStorage.setItem(storageKey, theme);
            } catch (error) {
                return;
            }
        }

        function updateButtons(activeTheme) {
            var nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
            var label = nextTheme === 'dark' ? 'Modo oscuro' : 'Modo claro';
            var iconClass = nextTheme === 'dark' ? 'fa-moon' : 'fa-sun';

            themeButtons.forEach(function (button) {
                button.setAttribute('aria-label', 'Activar ' + label.toLowerCase());
                button.setAttribute('title', label);
                button.setAttribute('aria-pressed', activeTheme === 'dark' ? 'true' : 'false');

                var icon = button.querySelector('[data-theme-icon]');

                if (icon) {
                    icon.classList.remove('fa-moon', 'fa-sun');
                    icon.classList.add(iconClass);
                }

                var text = button.querySelector('[data-theme-text]');

                if (text) {
                    text.textContent = label;
                }
            });
        }

        function applyTheme(theme) {
            root.dataset.theme = theme;
            root.style.colorScheme = theme;
            updateButtons(theme);
        }

        function toggleTheme() {
            var activeTheme = root.dataset.theme === 'dark' ? 'dark' : 'light';
            var nextTheme = activeTheme === 'dark' ? 'light' : 'dark';

            persistTheme(nextTheme);
            applyTheme(nextTheme);
        }

        themeButtons.forEach(function (button) {
            button.addEventListener('click', toggleTheme);
        });

        if (window.matchMedia) {
            var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            var handleMediaChange = function () {
                if (!getStoredTheme()) {
                    applyTheme(resolveTheme());
                }
            };

            if (typeof mediaQuery.addEventListener === 'function') {
                mediaQuery.addEventListener('change', handleMediaChange);
            } else if (typeof mediaQuery.addListener === 'function') {
                mediaQuery.addListener(handleMediaChange);
            }
        }

        applyTheme(resolveTheme());
    }());
</script>
