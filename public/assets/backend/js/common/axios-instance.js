(function (window, document) {
    'use strict';

    if (typeof window.axios === 'undefined') {
        throw new Error('Axios must be loaded before axios-instance.js');
    }

    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var appUrl = document.querySelector('meta[name="app-url"]');

    window.appAxios = window.axios.create({
        baseURL: appUrl ? appUrl.getAttribute('content') : '/',
        timeout: 15000,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        }
    });

    window.appAxios.interceptors.response.use(
        function (response) {
            return response;
        },
        function (error) {
            if (error.response && error.response.status === 419) {
                window.dispatchEvent(new CustomEvent('app:session-expired'));
            }

            return Promise.reject(error);
        }
    );
})(window, document);
