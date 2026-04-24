<script>
    (function () {
        var root = document.documentElement;

        try {
            var storedTheme = localStorage.getItem('frangy-control-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = storedTheme === 'light' || storedTheme === 'dark'
                ? storedTheme
                : (prefersDark ? 'dark' : 'light');

            root.dataset.theme = theme;
            root.style.colorScheme = theme;
        } catch (error) {
            root.dataset.theme = 'light';
            root.style.colorScheme = 'light';
        }
    }());
</script>
