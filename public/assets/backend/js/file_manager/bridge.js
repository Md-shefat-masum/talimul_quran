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

    function parseUsageContext(trigger) {
        if (!trigger || !trigger.dataset.fileManagerUsageModule) {
            return null;
        }

        return {
            module: trigger.dataset.fileManagerUsageModule,
            owner_type: trigger.dataset.fileManagerOwnerType || trigger.dataset.fileManagerUsageOwnerType || '',
            owner_id: trigger.dataset.fileManagerOwnerId || trigger.dataset.fileManagerUsageOwnerId || '',
            field_name: trigger.dataset.fileManagerUsageField || trigger.dataset.fileManagerField || '',
            collection: trigger.dataset.fileManagerUsageCollection || '',
            label: trigger.dataset.fileManagerUsageLabel || ''
        };
    }

    function formatSelectionValue(values, opener) {
        if (opener.valueFormat === 'json') {
            return JSON.stringify(values);
        }

        return opener.multiple ? values.join(',') : (values[0] || '');
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
                target.dataset.selectedPaths = JSON.stringify(paths);
                target.dataset.selectedUrls = JSON.stringify(urls);

                if (target.type !== 'file') {
                    target.value = formatSelectionValue(urls, opener);
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

            if (
                opener.usage &&
                opener.usage.module &&
                opener.usage.field_name &&
                (!window.FileManagerBridge.store || window.FileManagerBridge.store.canTrackUsage)
            ) {
                window.FileManagerApi.trackUsage(items, opener.usage).catch(function (error) {
                    window.console.warn('File manager usage tracking failed.', error);
                });
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
                    valueFormat: trigger.dataset.fileManagerValueFormat || (trigger.hasAttribute('data-file-manager-multiple') ? 'json' : 'string'),
                    path: trigger.dataset.fileManagerPath || '',
                    usage: parseUsageContext(trigger)
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
