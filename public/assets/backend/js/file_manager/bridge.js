(function (window, document) {
    'use strict';

    function parseSize(value) {
        if (!value) {
            return null;
        }

        var parts = String(value).toLowerCase().split('x');
        var width = Number(parts[0]);
        var height = Number(parts[1]);

        if (!width || !height) {
            return null;
        }

        return {width: width, height: height};
    }

    function resolveTarget(target) {
        if (!target) {
            return null;
        }

        if (typeof target === 'string') {
            return document.querySelector(target);
        }

        return target;
    }

    window.FileManagerBridge = {
        store: null,

        bindStore: function (store) {
            this.store = store;
        },

        open: function (options) {
            if (!this.store) {
                return;
            }

            options = options || {};
            options.target = resolveTarget(options.target);
            this.store.open(options);
        },

        applySelection: function (items, opener) {
            var target = resolveTarget(opener.target);
            var paths = items.map(function (item) {
                return item.path;
            });
            var urls = items.map(function (item) {
                return item.url || item.path;
            });

            if (target) {
                if (target.type === 'file') {
                    target.dataset.selectedPaths = JSON.stringify(paths);
                    target.dataset.selectedUrls = JSON.stringify(urls);
                } else {
                    target.value = opener.multiple ? urls.join(',') : urls[0];
                }

                target.dispatchEvent(new Event('change', {bubbles: true}));
                target.dispatchEvent(new CustomEvent('file-manager:selected', {
                    bubbles: true,
                    detail: {items: items, paths: paths, urls: urls}
                }));
            }

            if (typeof opener.callback === 'function') {
                opener.callback(items, urls, paths);
            }
        },

        bindTriggers: function () {
            document.addEventListener('click', function (event) {
                var trigger = event.target.closest('[data-file-manager]');

                if (!trigger || !window.FileManagerBridge.store) {
                    return;
                }

                event.preventDefault();

                window.FileManagerBridge.open({
                    target: trigger.dataset.fileManagerTarget || trigger.dataset.target || null,
                    multiple: trigger.dataset.fileManagerMultiple === 'true' || trigger.hasAttribute('data-file-manager-multiple'),
                    accept: trigger.dataset.fileManagerAccept || 'image/*',
                    size: parseSize(trigger.dataset.fileManagerSize),
                    path: trigger.dataset.fileManagerPath || ''
                });
            });
        }
    };

    window.FileManager = {
        open: function (options) {
            window.FileManagerBridge.open(options);
        }
    };
})(window, document);
