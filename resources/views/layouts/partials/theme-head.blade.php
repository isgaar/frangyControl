<script>
    (function () {
        var storageKey = 'frangy-control-theme';
        var storedTheme = null;

        try {
            storedTheme = localStorage.getItem(storageKey);
        } catch (error) {
            storedTheme = null;
        }

        var preferredTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light';

        document.documentElement.setAttribute('data-theme', storedTheme || preferredTheme);
    }());
</script>
