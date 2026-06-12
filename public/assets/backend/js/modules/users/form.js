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
            avatar_url: formData.get('avatar_url'),
            avatar_path: formData.get('avatar_path'),
            document_urls: formData.get('document_urls'),
            document_paths: formData.get('document_paths'),
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

    function getAvatarElements(form) {
        var picker = form.querySelector('[data-file-manager-picker][data-picker-field="avatar_url"]');

        return {
            urlInput: form.querySelector('[name="avatar_url"]'),
            pathInput: form.querySelector('[name="avatar_path"]'),
            displayInput: picker ? picker.querySelector('.js-file-manager-display') : null,
            preview: picker ? picker.querySelector('[data-file-manager-preview]') : null,
            trigger: picker ? picker.querySelector('[data-file-manager-target]') : null
        };
    }

    function renderAvatarPreview(preview, url) {
        if (!preview) {
            return;
        }

        preview.innerHTML = url
            ? '<img src="' + url + '" alt="Selected avatar">'
            : '<i class="mdi mdi-account-circle-outline"></i>';
    }

    function setAvatar(form, url, path) {
        var elements = getAvatarElements(form);

        if (elements.urlInput) {
            elements.urlInput.value = url || '';
        }

        if (elements.pathInput) {
            elements.pathInput.value = path || '';
        }

        if (elements.displayInput) {
            elements.displayInput.value = url || '';
        }

        renderAvatarPreview(elements.preview, url);
    }

    function syncAvatarFromSelection(form) {
        var elements = getAvatarElements(form);
        var paths = [];

        if (!elements.urlInput) {
            return;
        }

        try {
            paths = JSON.parse(elements.urlInput.dataset.selectedPaths || '[]');
        } catch (error) {
            paths = [];
        }

        setAvatar(form, elements.urlInput.value, paths[0] || '');
    }

    function setAvatarUsageContext(form, user) {
        var elements = getAvatarElements(form);

        if (!elements.trigger) {
            return;
        }

        elements.trigger.dataset.fileManagerOwnerId = user && user.id ? String(user.id) : '';
        elements.trigger.dataset.fileManagerUsageLabel = user && user.name ? user.name + ' avatar' : 'User avatar';
    }

    function getDocumentElements(form) {
        var picker = form.querySelector('[data-file-manager-picker][data-picker-field="document_urls"]');

        return {
            urlInput: form.querySelector('[name="document_urls"]'),
            pathInput: form.querySelector('[name="document_paths"]'),
            displayInput: picker ? picker.querySelector('.js-file-manager-display') : null,
            gallery: picker ? picker.querySelector('[data-file-manager-gallery]') : null,
            trigger: picker ? picker.querySelector('[data-file-manager-target]') : null
        };
    }

    function toJsonArray(values) {
        if (Array.isArray(values)) {
            return JSON.stringify(values.filter(Boolean));
        }

        if (typeof values === 'string' && values.trim() !== '') {
            try {
                var decoded = JSON.parse(values);

                return JSON.stringify(Array.isArray(decoded) ? decoded.filter(Boolean) : []);
            } catch (error) {
                return JSON.stringify([]);
            }
        }

        return JSON.stringify([]);
    }

    function setDocuments(form, urls, paths) {
        var elements = getDocumentElements(form);
        var parsedUrls = Array.isArray(urls) ? urls.filter(Boolean) : [];
        var parsedPaths = Array.isArray(paths) ? paths.filter(Boolean) : [];

        if (elements.urlInput) {
            elements.urlInput.value = toJsonArray(parsedUrls);
        }

        if (elements.pathInput) {
            elements.pathInput.value = toJsonArray(parsedPaths);
        }

        if (elements.displayInput) {
            elements.displayInput.value = parsedUrls.length ? parsedUrls.length + ' file(s) selected' : '';
        }

        if (elements.gallery) {
            elements.gallery.innerHTML = parsedUrls.map(function (url, index) {
                var name = url.split('/').pop() || ('Document ' + (index + 1));

                return '<button type="button" class="file-manager-picker__chip" data-file-manager-remove-index="' + index + '">' +
                    '<img src="' + url + '" alt="Document ' + (index + 1) + '">' +
                    '<span>' + name + '</span>' +
                    '<i class="mdi mdi-close"></i>' +
                    '</button>';
            }).join('');
        }
    }

    function setDocumentUsageContext(form, user) {
        var elements = getDocumentElements(form);

        if (!elements.trigger) {
            return;
        }

        elements.trigger.dataset.fileManagerOwnerId = user && user.id ? String(user.id) : '';
        elements.trigger.dataset.fileManagerUsageLabel = user && user.name ? user.name + ' documents' : 'User documents';
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
        setAvatar(form, '', '');
        setAvatarUsageContext(form, null);
        setDocuments(form, [], []);
        setDocumentUsageContext(form, null);

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
        setAvatar(form, user.avatar_url || '', user.avatar_path || '');
        setAvatarUsageContext(form, user);
        setDocuments(form, user.document_urls || [], user.document_paths || []);
        setDocumentUsageContext(form, user);
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

        form.addEventListener('file-manager:selected', function (event) {
            if (event.target && event.target.name === 'avatar_url') {
                syncAvatarFromSelection(form);
            }
        });

        var clearAvatarButton = form.querySelector('[data-file-manager-picker][data-picker-field="avatar_url"] .js-file-manager-clear');
        if (clearAvatarButton) {
            clearAvatarButton.addEventListener('click', function () {
                setAvatar(form, '', '');
            });
        }

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
