<script>
    (function () {
        var storageKey = 'frangy-control-theme';
        var root = document.documentElement;
        var buttons = document.querySelectorAll('[data-theme-toggle]');

        function currentTheme() {
            return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        }

        function updateControls(theme) {
            buttons.forEach(function (button) {
                var icon = button.querySelector('[data-theme-icon]');
                var text = button.querySelector('[data-theme-text]');
                var isDark = theme === 'dark';

                button.setAttribute('aria-label', isDark ? 'Activar modo claro' : 'Activar modo oscuro');

                if (icon) {
                    icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
                }

                if (text) {
                    text.textContent = isDark ? 'Modo claro' : 'Modo oscuro';
                }
            });
        }

        function setTheme(theme) {
            root.setAttribute('data-theme', theme);

            try {
                localStorage.setItem(storageKey, theme);
            } catch (error) {
                return;
            }

            updateControls(theme);
        }

        updateControls(currentTheme());

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
            });
        });
    }());
</script>
