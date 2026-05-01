<script>
    (function () {
        var storageKey = 'frangy-control-theme';
        var storedTheme = null;

        try {
            storedTheme = localStorage.getItem(storageKey);
        } catch (error) {
            storedTheme = null;
        }

        document.documentElement.setAttribute('data-theme', storedTheme || 'light');
    }());
</script>
