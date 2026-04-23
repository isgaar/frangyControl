(function () {
    function createLoadingMarkup(text) {
        return '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + text;
    }

    function resolveElement(target) {
        if (!target) {
            return null;
        }

        if (typeof target === 'string') {
            return document.querySelector(target);
        }

        return target;
    }

    function attachSubmitLoading(formTarget, buttonTarget, loadingText) {
        var form = resolveElement(formTarget);
        var button = resolveElement(buttonTarget);

        if (!form || !button) {
            return;
        }

        var originalHtml = button.innerHTML;

        form.addEventListener('submit', function () {
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                return;
            }

            button.disabled = true;
            button.classList.add('disabled');
            button.innerHTML = createLoadingMarkup(loadingText || 'Procesando...');
            button.dataset.originalHtml = originalHtml;
        });
    }

    function togglePasswordVisibility(fieldId, trigger) {
        var field = document.getElementById(fieldId);

        if (!field) {
            return;
        }

        field.type = field.type === 'password' ? 'text' : 'password';

        if (!trigger) {
            return;
        }

        var icon = trigger.querySelector('i');

        if (!icon) {
            return;
        }

        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    window.FormHelpers = {
        attachSubmitLoading: attachSubmitLoading,
        togglePasswordVisibility: togglePasswordVisibility
    };
}());
