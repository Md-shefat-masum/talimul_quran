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
        success: function (message) {
            showToast('success', message);
        },

        error: function (message) {
            showToast('error', message || 'Something went wrong. Please try again.');
        },

        errorFromException: function (error) {
            var response = error && error.response ? error.response : null;
            var message = response && response.data && response.data.message
                ? response.data.message
                : 'Something went wrong. Please try again.';

            this.error(message);
        },

        confirmDelete: function (name) {
            if (typeof window.Swal === 'undefined') {
                return Promise.resolve(window.confirm('Delete ' + name + '?'));
            }

            return window.Swal.fire({
                title: 'Delete this user?',
                text: name + ' will be moved to soft-deleted records.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete user',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            }).then(function (result) {
                return result.isConfirmed;
            });
        }
    };

    window.addEventListener('app:session-expired', function () {
        window.appAlert.error('Your session has expired. Refresh the page and log in again.');
    });
})(window);
