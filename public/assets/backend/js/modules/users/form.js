(function (window, document, $) {
    'use strict';

    function replaceUserId(urlTemplate, userId) {
        return urlTemplate.replace('__USER_ID__', String(userId));
    }

    function setLoading(form, isLoading) {
        var button = form.querySelector('.js-user-form-submit');
        var idle = form.querySelector('.js-submit-idle');
        var loading = form.querySelector('.js-submit-loading');

        if (!button || !idle || !loading) {
            return;
        }

        button.disabled = isLoading;
        idle.classList.toggle('d-none', isLoading);
        loading.classList.toggle('d-none', !isLoading);
    }

    function getPayload(form) {
        var formData = new FormData(form);

        return {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            user_type_id: formData.get('user_type_id'),
            status: formData.get('status'),
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation')
        };
    }

    function setModalTitle(form, title) {
        var modal = form.closest('.modal');
        var titleElement = modal ? modal.querySelector('.modal-title') : null;

        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    function updateSubmitText(form, text) {
        var idle = form.querySelector('.js-submit-idle');

        if (idle) {
            idle.innerHTML = '<i class="mdi mdi-content-save-outline me-1"></i>' + text;
        }
    }

    function reset(form) {
        form.reset();
        form.dataset.mode = 'create';
        form.dataset.userId = '';
        window.formErrorHelper.clear(form);

        var userType = $(form).find('.js-user-type-select');
        userType.val(null).trigger('change');

        setModalTitle(form, 'Create User');
        updateSubmitText(form, 'Save User');
    }

    function populate(form, user) {
        reset(form);

        form.dataset.mode = 'edit';
        form.dataset.userId = user.id;
        form.querySelector('[name="name"]').value = user.name || '';
        form.querySelector('[name="email"]').value = user.email || '';
        form.querySelector('[name="phone"]').value = user.phone || '';
        form.querySelector('[name="status"]').value = String(user.status);
        form.querySelector('[name="password"]').value = '';
        form.querySelector('[name="password_confirmation"]').value = '';

        var userTypeSelect = $(form).find('.js-user-type-select');
        if (user.user_type_id) {
            var option = new Option(user.user_type_text || 'Selected user type', user.user_type_id, true, true);
            userTypeSelect.append(option).trigger('change');
        }

        setModalTitle(form, 'Edit User');
        updateSubmitText(form, 'Update User');
    }

    function handleSubmit(form) {
        window.formErrorHelper.clear(form);
        setLoading(form, true);

        var isEdit = form.dataset.mode === 'edit';
        var userId = form.dataset.userId;
        var url = isEdit
            ? replaceUserId(form.dataset.updateUrlTemplate, userId)
            : form.dataset.storeUrl;
        var request = isEdit
            ? window.appAxios.put(url, getPayload(form))
            : window.appAxios.post(url, getPayload(form));

        request.then(function (response) {
            window.appAlert.success(response.data.message || 'Saved successfully.');

            if (form.dataset.context === 'modal') {
                var modalElement = form.closest('.modal');
                var modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.hide();
                reset(form);
                document.dispatchEvent(new CustomEvent('user:changed'));
                return;
            }

            window.location.href = form.dataset.indexUrl;
        }).catch(function (error) {
            if (error.response && error.response.status === 422) {
                window.formErrorHelper.show(form, error.response.data.errors || {});
                window.appAlert.error(error.response.data.message || 'Please correct the highlighted fields.');
                return;
            }

            window.appAlert.errorFromException(error);
        }).finally(function () {
            setLoading(form, false);
        });
    }

    function initialize(form) {
        if (form.dataset.initialized === 'true') {
            return;
        }

        var userTypeSelect = form.querySelector('.js-user-type-select');
        if (userTypeSelect) {
            window.initAxiosSelect2(userTypeSelect, {
                url: form.dataset.userTypeOptionsUrl,
                placeholder: userTypeSelect.dataset.placeholder,
                allowClear: false
            });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            handleSubmit(form);
        });

        form.dataset.initialized = 'true';
    }

    window.UserFormManager = {
        initialize: initialize,
        reset: reset,
        populate: populate
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-user-form').forEach(initialize);
    });
})(window, document, window.jQuery);
