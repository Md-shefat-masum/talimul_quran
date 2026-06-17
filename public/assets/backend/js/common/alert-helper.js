(function (window) {
    'use strict';

    function showToast(icon, message) {
        if (typeof window.Swal === 'undefined') {
            window.alert(message);
            return;
        }

        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 2800,
            timerProgressBar: true
        });
    }

    window.appAlert = {
        show: function (type, message) {
            showToast(type || 'info', message || '');
        },

        success: function (message) {
            showToast('success', message);
        },

        error: function (message) {
            showToast('error', message || 'Something went wrong. Please try again.');
        },

        warning: function (message) {
            showToast('warning', message || 'Please check the details and try again.');
        },

        info: function (message) {
            showToast('info', message || 'Heads up.');
        },

        errorFromException: function (error) {
            var response = error && error.response ? error.response : null;
            var message = response && response.data && response.data.message
                ? response.data.message
                : 'Something went wrong. Please try again.';

            this.error(message);
        },

        confirm: function (options) {
            options = options || {};

            if (typeof window.Swal === 'undefined') {
                return Promise.resolve(window.confirm(options.text || options.title || 'Are you sure?'));
            }

            return window.Swal.fire({
                title: options.title || 'Are you sure?',
                text: options.text || '',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmButtonText || 'Yes, continue',
                cancelButtonText: options.cancelButtonText || 'Cancel',
                confirmButtonColor: options.confirmButtonColor || '#dc3545'
            }).then(function (result) {
                return result.isConfirmed;
            });
        },

        confirmDelete: function (name) {
            return this.confirm({
                title: 'Delete this user?',
                text: name + ' will be moved to soft-deleted records.',
                icon: 'warning',
                confirmButtonText: 'Yes, delete user',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            });
        }
    };

    function showFlashAlerts() {
        document.querySelectorAll('[data-app-flash-alerts]').forEach(function (element) {
            var alerts = [];

            try {
                alerts = JSON.parse(element.textContent || '[]');
            } catch (error) {
                alerts = [];
            }

            alerts.forEach(function (alert) {
                if (!alert || !alert.message) {
                    return;
                }

                window.appAlert.show(alert.type || 'info', alert.message);
            });

            element.remove();
        });
    }

    function bindConfirmForms() {
        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (!form.matches('[data-confirm-submit]') || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            window.appAlert.confirm({
                title: form.dataset.confirmTitle,
                text: form.dataset.confirmText,
                icon: form.dataset.confirmIcon || 'warning',
                confirmButtonText: form.dataset.confirmButtonText,
                cancelButtonText: form.dataset.confirmCancelText,
                confirmButtonColor: form.dataset.confirmButtonColor
            }).then(function (confirmed) {
                if (!confirmed) {
                    return;
                }

                form.dataset.confirmed = 'true';

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                    return;
                }

                form.submit();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showFlashAlerts);
        document.addEventListener('DOMContentLoaded', bindConfirmForms);
    } else {
        showFlashAlerts();
        bindConfirmForms();
    }

    window.addEventListener('app:session-expired', function () {
        window.appAlert.error('Your session has expired. Refresh the page and log in again.');
    });
})(window);
