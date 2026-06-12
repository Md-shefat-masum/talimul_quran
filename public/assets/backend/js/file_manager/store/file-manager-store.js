(function (window) {
    'use strict';

    window.FileManagerStores = window.FileManagerStores || {};

    window.FileManagerStores.useFileManagerStore = window.Pinia.defineStore('fileManager', {
        state: function () {
            return {
                isOpen: false,
                path: '',
                breadcrumbs: [{label: 'Home', path: ''}],
                items: [],
                selected: [],
                activeItem: null,
                viewMode: 'grid',
                query: '',
                loading: false,
                saving: false,
                error: '',
                mode: 'browse',
                opener: {
                    target: null,
                    multiple: false,
                    accept: 'image/*',
                    size: null,
                    callback: null
                }
            };
        },

        getters: {
            filteredItems: function (state) {
                var query = state.query.trim().toLowerCase();
                var items = state.items.slice().sort(function (a, b) {
                    if (a.type !== b.type) {
                        return a.type === 'directory' ? -1 : 1;
                    }

                    return a.name.localeCompare(b.name);
                });

                if (!query) {
                    return items;
                }

                return items.filter(function (item) {
                    return item.name.toLowerCase().indexOf(query) !== -1;
                });
            },

            selectedPaths: function (state) {
                return state.selected.map(function (item) {
                    return item.path;
                });
            }
        },

        actions: {
            open: function (options) {
                options = options || {};
                this.opener = {
                    target: options.target || null,
                    multiple: Boolean(options.multiple),
                    accept: options.accept || window.FileManagerConfig.defaults.accept,
                    size: options.size || null,
                    callback: options.callback || null
                };
                this.selected = [];
                this.activeItem = null;
                this.error = '';
                this.isOpen = true;
                this.mode = options.mode || 'browse';
                this.load(options.path || this.path || window.FileManagerConfig.defaults.path);
            },

            close: function () {
                this.isOpen = false;
                this.mode = 'browse';
            },

            load: function (path) {
                var store = this;
                store.loading = true;
                store.error = '';

                return window.FileManagerApi.list(path || '').then(function (data) {
                    store.path = data.path || '';
                    store.breadcrumbs = data.breadcrumbs || [{label: 'Home', path: ''}];
                    store.items = data.items || [];
                    store.selected = [];
                    store.activeItem = null;
                    store.error = data.error || '';
                }).catch(function (error) {
                    store.items = [];
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not load files from the FTP disk.';
                }).finally(function () {
                    store.loading = false;
                });
            },

            openFolder: function (item) {
                if (item.type === 'directory') {
                    this.load(item.path);
                }
            },

            toggleItem: function (item) {
                var exists = this.selected.some(function (selectedItem) {
                    return selectedItem.path === item.path;
                });

                this.activeItem = item;

                if (item.type !== 'file') {
                    this.selected = [];
                    return;
                }

                if (!this.opener.multiple) {
                    this.selected = exists ? [] : [item];
                    return;
                }

                this.selected = exists
                    ? this.selected.filter(function (selectedItem) {
                        return selectedItem.path !== item.path;
                    })
                    : this.selected.concat([item]);
            },

            setMode: function (mode) {
                this.mode = mode;
            },

            uploadPhoto: function (payload) {
                var store = this;
                store.saving = true;
                store.error = '';

                return window.FileManagerApi.uploadPhoto(payload).then(function (item) {
                    store.mode = 'browse';
                    return store.load(store.path).then(function () {
                        store.selected = [item];
                        store.activeItem = item;
                        return item;
                    });
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not upload this photo.';
                    throw error;
                }).finally(function () {
                    store.saving = false;
                });
            },

            createFolder: function (name) {
                var store = this;

                if (!name) {
                    return Promise.resolve();
                }

                store.saving = true;

                return window.FileManagerApi.createFolder(store.path, name).then(function () {
                    return store.load(store.path);
                }).finally(function () {
                    store.saving = false;
                });
            },

            destroy: function (item) {
                var store = this;
                store.saving = true;

                return window.FileManagerApi.destroy(item).then(function () {
                    return store.load(store.path);
                }).finally(function () {
                    store.saving = false;
                });
            },

            useSelected: function () {
                if (!this.selected.length) {
                    return;
                }

                window.FileManagerBridge.applySelection(this.selected, this.opener);
                this.close();
            }
        }
    });
})(window);
