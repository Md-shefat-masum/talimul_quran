(function (window, document) {
    'use strict';

    function parseList(value) {
        var decoded;

        if (Array.isArray(value)) {
            return value.filter(Boolean);
        }

        if (!value) {
            return [];
        }

        try {
            decoded = JSON.parse(value);

            if (Array.isArray(decoded)) {
                return decoded.filter(Boolean);
            }
        } catch (error) {
            decoded = null;
        }

        return String(value).split(',').map(function (item) {
            return item.trim();
        }).filter(Boolean);
    }

    function fileNameFromUrl(url) {
        var path;

        try {
            path = new URL(url, window.location.origin).pathname;
        } catch (error) {
            path = url;
        }

        return decodeURIComponent(String(path).split('/').pop() || url);
    }

    function isMultiple(picker) {
        return picker && picker.hasAttribute('data-file-manager-picker-multiple');
    }

    function valueFormat(picker) {
        return picker ? (picker.dataset.fileManagerValueFormat || 'string') : 'string';
    }

    function formatValue(values, picker) {
        if (isMultiple(picker) && valueFormat(picker) === 'json') {
            return JSON.stringify(values);
        }

        return isMultiple(picker) ? values.join(',') : (values[0] || '');
    }

    function pickerElements(picker) {
        return {
            valueInput: picker.querySelector('[data-file-manager-value-field]'),
            pathInput: picker.querySelector('[data-file-manager-path-field]'),
            displayInput: picker.querySelector('.js-file-manager-display'),
            preview: picker.querySelector('[data-file-manager-preview]'),
            gallery: picker.querySelector('[data-file-manager-gallery]')
        };
    }

    function renderSinglePreview(preview, url) {
        var image;
        var icon;

        if (!preview) {
            return;
        }

        preview.textContent = '';

        if (url) {
            image = document.createElement('img');
            image.src = url;
            image.alt = 'Selected file';
            preview.appendChild(image);
            return;
        }

        icon = document.createElement('i');
        icon.className = 'mdi mdi-image-outline';
        preview.appendChild(icon);
    }

    function renderGallery(gallery, urls) {
        var fragment;

        if (!gallery) {
            return;
        }

        gallery.textContent = '';

        if (!urls.length) {
            return;
        }

        fragment = document.createDocumentFragment();

        urls.forEach(function (url, index) {
            var button = document.createElement('button');
            var image = document.createElement('img');
            var label = document.createElement('span');
            var icon = document.createElement('i');

            button.type = 'button';
            button.className = 'file-manager-picker__chip';
            button.dataset.fileManagerRemoveIndex = String(index);

            image.src = url;
            image.alt = 'Selected file ' + (index + 1);
            label.textContent = fileNameFromUrl(url);
            icon.className = 'mdi mdi-close';

            button.appendChild(image);
            button.appendChild(label);
            button.appendChild(icon);
            fragment.appendChild(button);
        });

        gallery.appendChild(fragment);
    }

    function setPickerValues(picker, urls, paths) {
        var elements = pickerElements(picker);
        var multiple = isMultiple(picker);

        urls = parseList(urls);
        paths = parseList(paths);

        if (!multiple && urls.length > 1) {
            urls = urls.slice(0, 1);
            paths = paths.slice(0, 1);
        }

        if (elements.valueInput) {
            elements.valueInput.value = formatValue(urls, picker);
            elements.valueInput.dataset.selectedUrls = JSON.stringify(urls);
            elements.valueInput.dataset.selectedPaths = JSON.stringify(paths);
        }

        if (elements.pathInput) {
            elements.pathInput.value = multiple ? JSON.stringify(paths) : (paths[0] || '');
        }

        if (elements.displayInput) {
            elements.displayInput.value = multiple
                ? (urls.length ? urls.length + ' file(s) selected' : '')
                : (urls[0] || '');
        }

        if (multiple) {
            renderSinglePreview(elements.preview, '');
            renderGallery(elements.gallery, urls);
            return;
        }

        renderSinglePreview(elements.preview, urls[0] || '');
    }

    function closestPickerFromInput(input) {
        return input ? input.closest('[data-file-manager-picker]') : null;
    }

    function initializePicker(picker) {
        var elements = pickerElements(picker);
        var urls = elements.valueInput ? parseList(elements.valueInput.value) : [];
        var paths = elements.pathInput ? parseList(elements.pathInput.value) : [];

        setPickerValues(picker, urls, paths);
    }

    document.addEventListener('file-manager:selected', function (event) {
        var picker = closestPickerFromInput(event.target);
        var detail = event.detail || {};

        if (!picker) {
            return;
        }

        setPickerValues(picker, detail.urls || [], detail.paths || []);
    });

    document.addEventListener('click', function (event) {
        var clearButton = event.target.closest('.js-file-manager-clear');
        var removeButton = event.target.closest('[data-file-manager-remove-index]');
        var picker;
        var elements;
        var urls;
        var paths;
        var removeIndex;

        if (clearButton) {
            picker = clearButton.closest('[data-file-manager-picker]');

            if (picker) {
                setPickerValues(picker, [], []);
            }

            return;
        }

        if (!removeButton) {
            return;
        }

        picker = removeButton.closest('[data-file-manager-picker]');

        if (!picker) {
            return;
        }

        elements = pickerElements(picker);
        urls = elements.valueInput ? parseList(elements.valueInput.value) : [];
        paths = elements.pathInput ? parseList(elements.pathInput.value) : [];
        removeIndex = Number(removeButton.dataset.fileManagerRemoveIndex);

        if (Number.isNaN(removeIndex)) {
            return;
        }

        urls.splice(removeIndex, 1);
        paths.splice(removeIndex, 1);
        setPickerValues(picker, urls, paths);
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-file-manager-picker]').forEach(initializePicker);
    });

    window.FileManagerPicker = {
        initialize: initializePicker,
        setValues: setPickerValues
    };
})(window, document);
