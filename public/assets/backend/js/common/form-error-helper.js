(function (window) {
    'use strict';

    window.formErrorHelper = {
        clear: function (form) {
            form.querySelectorAll('.is-invalid').forEach(function (element) {
                element.classList.remove('is-invalid');
            });

            form.querySelectorAll('[data-error-for]').forEach(function (element) {
                element.textContent = '';
            });
        },

        show: function (form, errors) {
            this.clear(form);

            Object.keys(errors || {}).forEach(function (fieldName) {
                var field = form.querySelector('[name="' + fieldName + '"]');
                var errorElement = form.querySelector('[data-error-for="' + fieldName + '"]');
                var messages = errors[fieldName];

                if (field) {
                    field.classList.add('is-invalid');
                }

                if (errorElement) {
                    errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                }
            });
        }
    };
})(window);
