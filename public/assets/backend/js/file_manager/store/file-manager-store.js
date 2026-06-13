(function (window) {
    'use strict';

    window.FileManagerStores = window.FileManagerStores || {};

    window.FileManagerStores.useFileManagerStore = window.Pinia.defineStore('fileManager', {
        state: function () {
            return {
                isOpen: false,
                path: '',
                currentFolderId: null,
                breadcrumbs: [{label: 'Home', path: ''}],
                items: [],
                selected: [],
                activeItem: null,
                viewMode: 'grid',
                query: '',
                queryTimer: null,
                loading: false,
                loadingMore: false,
                saving: false,
                error: '',
                mode: 'browse',
                permissions: Object.assign({}, window.FileManagerConfig.permissions),
                pagination: {
                    page: 1,
                    perPage: 60,
                    total: 0,
                    shown: 0,
                    hasMore: false,
                    nextPage: null
                },
                uploadProgress: 0,
                uploadConflict: null,
                thumbnailCache: {
                    files: 0,
                    bytes: 0,
                    bytes_label: '0 B',
                    path: ''
                },
                thumbnailCacheLoading: false,
                importSummary: null,
                importHistory: [],
                importHistoryLoading: false,
                importingMedia: false,
                recentFolders: [],
                moveDialog: {
                    isOpen: false,
                    item: null,
                    destination: ''
                },
                deleteDialog: {
                    isOpen: false,
                    item: null,
                    usage: null,
                    loadingUsage: false
                },
                dragDrop: {
                    item: null,
                    overPath: '',
                    overKey: ''
                },
                opener: {
                    target: null,
                    multiple: false,
                    accept: 'image/*',
                    size: null,
                    valueFormat: 'string',
                    usage: null,
                    callback: null
                }
            };
        },

        getters: {
            filteredItems: function (state) {
                var items = state.items.slice().sort(function (a, b) {
                    if (a.type !== b.type) {
                        return a.type === 'directory' ? -1 : 1;
                    }

                    return a.name.localeCompare(b.name);
                });

                return items;
            },

            selectedPaths: function (state) {
                return state.selected.map(function (item) {
                    return item.path;
                });
            },

            selectedMediaIds: function (state) {
                return state.selected.map(function (item) {
                    return item.media_id || item.id || null;
                }).filter(Boolean);
            },

            directoryItems: function (state) {
                return state.items.filter(function (item) {
                    return item.type === 'directory';
                }).sort(function (a, b) {
                    return a.name.localeCompare(b.name);
                });
            },

            canRead: function (state) {
                return Boolean(state.permissions.read);
            },

            canUpload: function (state) {
                return Boolean(state.permissions.upload);
            },

            canCreateFolder: function (state) {
                return Boolean(state.permissions.create_folder);
            },

            canRename: function (state) {
                return Boolean(state.permissions.rename);
            },

            canMove: function (state) {
                return Boolean(state.permissions.move);
            },

            canDelete: function (state) {
                return Boolean(state.permissions.delete);
            },

            canForceDelete: function (state) {
                return Boolean(state.permissions.force_delete);
            },

            canTrackUsage: function (state) {
                return Boolean(state.permissions.track_usage);
            },

            canMaintenance: function (state) {
                return Boolean(state.permissions.maintenance);
            }
        },

        actions: {
            setPermissionError: function () {
                this.error = 'You do not have permission to use this file manager action.';
            },

            itemKey: function (item) {
                if (!item) {
                    return '';
                }

                if (item.type === 'file' && (item.media_id || item.id)) {
                    return 'file:' + (item.media_id || item.id);
                }

                if (item.type === 'directory' && (item.folder_id || item.id)) {
                    return 'directory:' + (item.folder_id || item.id);
                }

                return (item.type || 'item') + ':' + (item.path || item.name || '');
            },

            sameItem: function (first, second) {
                return this.itemKey(first) !== '' && this.itemKey(first) === this.itemKey(second);
            },

            applyPagination: function (pagination) {
                pagination = pagination || {};
                this.pagination = {
                    page: Number(pagination.page || 1),
                    perPage: Number(pagination.per_page || this.pagination.perPage || 60),
                    total: Number(pagination.total || 0),
                    shown: Number(pagination.shown || 0),
                    hasMore: Boolean(pagination.has_more),
                    nextPage: pagination.next_page || null
                };
            },

            open: function (options) {
                var store = this;

                options = options || {};
                this.opener = {
                    target: options.target || null,
                    multiple: Boolean(options.multiple),
                    accept: options.accept || window.FileManagerConfig.defaults.accept,
                    size: options.size || null,
                    valueFormat: options.valueFormat || 'string',
                    usage: options.usage || null,
                    callback: options.callback || null
                };
                this.selected = [];
                this.activeItem = null;
                this.error = '';
                this.isOpen = true;
                this.mode = options.mode || 'browse';
                this.load(options.path || this.path || window.FileManagerConfig.defaults.path, 1).then(function () {
                    if (!store.canMaintenance) {
                        return null;
                    }

                    return Promise.all([
                        store.loadThumbnailCache(),
                        store.loadImportHistory()
                    ]);
                });
            },

            close: function () {
                this.isOpen = false;
                this.mode = 'browse';
            },

            load: function (path, page, append) {
                var store = this;
                var requestedPage = page || 1;

                if (append) {
                    store.loadingMore = true;
                } else {
                    store.loading = true;
                }

                store.error = '';

                return window.FileManagerApi.list(path || '', {
                    folderId: null,
                    page: requestedPage,
                    perPage: store.pagination.perPage,
                    query: store.query
                }).then(function (data) {
                    store.path = data.path || '';
                    store.currentFolderId = data.folder_id || null;
                    store.breadcrumbs = data.breadcrumbs || [{label: 'Home', path: ''}];
                    store.items = append ? store.items.concat(data.items || []) : (data.items || []);
                    store.applyPagination(data.pagination);
                    store.permissions = Object.assign({}, store.permissions, data.permissions || {});
                    if (!append) {
                        store.selected = [];
                        store.activeItem = null;
                    }
                    store.error = data.error || '';
                    store.rememberFolder(store.path);
                }).catch(function (error) {
                    if (!append) {
                        store.items = [];
                        store.applyPagination(null);
                    }
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not load files from the FTP disk.';
                    if (store.error === 'Could not load files from the FTP disk.') {
                        store.error = 'Could not load media from the database library.';
                    }
                }).finally(function () {
                    store.loading = false;
                    store.loadingMore = false;
                });
            },

            loadMore: function () {
                if (!this.pagination.hasMore || this.loading || this.loadingMore) {
                    return Promise.resolve();
                }

                return this.load(this.path, this.pagination.nextPage || (this.pagination.page + 1), true);
            },

            search: function () {
                var store = this;

                clearTimeout(this.queryTimer);
                this.queryTimer = setTimeout(function () {
                    store.load(store.path, 1);
                }, 260);
            },

            openFolder: function (item) {
                if (item.type === 'directory') {
                    this.query = '';
                    this.load(item.path, 1);
                }
            },

            rememberFolder: function (path) {
                path = path || '';

                if (path === '') {
                    return;
                }

                this.recentFolders = [path].concat(this.recentFolders.filter(function (folderPath) {
                    return folderPath !== path;
                })).slice(0, 6);
            },

            toggleItem: function (item) {
                var exists = this.selected.some(function (selectedItem) {
                    return this.sameItem(selectedItem, item);
                }, this);

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
                        return !this.sameItem(selectedItem, item);
                    }, this)
                    : this.selected.concat([item]);
            },

            setMode: function (mode) {
                this.mode = mode;
            },

            loadThumbnailCache: function () {
                var store = this;

                if (!store.canMaintenance) {
                    return Promise.resolve(null);
                }

                store.thumbnailCacheLoading = true;

                return window.FileManagerApi.thumbnailCache().then(function (stats) {
                    store.thumbnailCache = Object.assign({}, store.thumbnailCache, stats || {});
                    return stats;
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not read thumbnail cache stats.';
                    return null;
                }).finally(function () {
                    store.thumbnailCacheLoading = false;
                });
            },

            clearThumbnailCache: function () {
                var store = this;

                if (!store.canMaintenance) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                store.thumbnailCacheLoading = true;

                return window.FileManagerApi.clearThumbnailCache().then(function () {
                    store.thumbnailCache = {
                        files: 0,
                        bytes: 0,
                        bytes_label: '0 B',
                        path: store.thumbnailCache.path || ''
                    };
                    return store.loadThumbnailCache();
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not clear thumbnail cache.';
                    throw error;
                }).finally(function () {
                    store.thumbnailCacheLoading = false;
                });
            },

            importMedia: function (payload) {
                var store = this;

                if (!store.canMaintenance) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                store.importingMedia = true;
                store.error = '';

                return window.FileManagerApi.importMedia(payload).then(function (summary) {
                    store.importSummary = summary;
                    return Promise.all([
                        store.load(store.path, 1),
                        store.loadImportHistory()
                    ]).then(function () {
                        return summary;
                    });
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not import storage files into the media database.';
                    throw error;
                }).finally(function () {
                    store.importingMedia = false;
                });
            },

            loadImportHistory: function () {
                var store = this;

                if (!store.canMaintenance) {
                    return Promise.resolve([]);
                }

                store.importHistoryLoading = true;

                return window.FileManagerApi.importHistory(5).then(function (items) {
                    store.importHistory = items || [];
                    return store.importHistory;
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not read import history.';
                    return [];
                }).finally(function () {
                    store.importHistoryLoading = false;
                });
            },

            uploadPhoto: function (payload) {
                var store = this;

                if (!store.canUpload) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                store.saving = true;
                store.error = '';
                store.uploadProgress = 0;
                store.uploadConflict = null;

                return window.FileManagerApi.uploadPhoto(payload).then(function (item) {
                    store.uploadProgress = 100;
                    store.mode = 'browse';
                    return store.load(store.path, 1).then(function () {
                        store.selected = [item];
                        store.activeItem = item;
                        return item;
                    });
                }).catch(function (error) {
                    var responseData = error.response && error.response.data ? error.response.data : null;

                    if (error.response && error.response.status === 409 && responseData && responseData.conflict) {
                        store.uploadConflict = responseData.conflict;
                    }

                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not upload this photo.';
                    throw error;
                }).finally(function () {
                    store.saving = false;
                    if (!store.uploadConflict) {
                        store.uploadProgress = 0;
                    }
                });
            },

            createFolder: function (name) {
                var store = this;

                if (!store.canCreateFolder) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                if (!name) {
                    return Promise.resolve();
                }

                store.saving = true;

                return window.FileManagerApi.createFolder(store.path, name, store.currentFolderId).then(function () {
                    return store.load(store.path, 1);
                }).finally(function () {
                    store.saving = false;
                });
            },

            renameItem: function (item, name) {
                var store = this;

                if (!store.canRename) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                if (!item || !name) {
                    return Promise.resolve();
                }

                store.saving = true;
                store.error = '';

                return window.FileManagerApi.rename(item, name).then(function (updatedItem) {
                    return store.load(store.path, 1).then(function () {
                        store.activeItem = updatedItem;
                        store.selected = updatedItem.type === 'file' ? [updatedItem] : [];
                        return updatedItem;
                    });
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not rename the selected item.';
                    throw error;
                }).finally(function () {
                    store.saving = false;
                });
            },

            moveItem: function (item, destination) {
                var store = this;

                if (!store.canMove) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                if (!item) {
                    return Promise.resolve();
                }

                store.saving = true;
                store.error = '';

                return window.FileManagerApi.move(item, destination || '').then(function (updatedItem) {
                    return store.load(store.path, 1).then(function () {
                        store.activeItem = store.items.some(function (currentItem) {
                            return store.sameItem(currentItem, updatedItem);
                        }) ? updatedItem : null;
                        store.selected = [];
                        return updatedItem;
                    });
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not move the selected item.';
                    throw error;
                }).finally(function () {
                    store.saving = false;
                });
            },

            openMoveDialog: function (item) {
                if (!item || !this.canMove) {
                    if (item && !this.canMove) {
                        this.setPermissionError();
                    }
                    return;
                }

                this.moveDialog = {
                    isOpen: true,
                    item: item,
                    destination: this.path || ''
                };
                this.activeItem = item;
            },

            openMoveDialogForDestination: function (item, destination) {
                if (!item || !this.canMove) {
                    if (item && !this.canMove) {
                        this.setPermissionError();
                    }
                    return;
                }

                this.moveDialog = {
                    isOpen: true,
                    item: item,
                    destination: destination || ''
                };
                this.activeItem = item;
            },

            closeMoveDialog: function () {
                this.moveDialog = {
                    isOpen: false,
                    item: null,
                    destination: ''
                };
            },

            openDeleteDialog: function (item) {
                var store = this;

                if (!item) {
                    return;
                }

                if (!store.canDelete) {
                    store.setPermissionError();
                    return;
                }

                store.activeItem = item;
                store.deleteDialog = {
                    isOpen: true,
                    item: item,
                    usage: item.usage || null,
                    loadingUsage: true
                };

                store.refreshUsage(item).then(function (usage) {
                    store.deleteDialog.usage = usage;
                }).catch(function () {
                    store.deleteDialog.usage = item.usage || null;
                }).finally(function () {
                    store.deleteDialog.loadingUsage = false;
                });
            },

            closeDeleteDialog: function () {
                this.deleteDialog = {
                    isOpen: false,
                    item: null,
                    usage: null,
                    loadingUsage: false
                };
            },

            confirmDelete: function (force) {
                var store = this;
                var item = store.deleteDialog.item;

                if (!item || store.saving) {
                    return Promise.resolve(null);
                }

                return store.destroy(item, Boolean(force)).then(function (response) {
                    store.closeDeleteDialog();
                    return response;
                });
            },

            confirmMove: function () {
                var store = this;
                var item = this.moveDialog.item;
                var destination = this.moveDialog.destination || '';

                if (!item) {
                    this.closeMoveDialog();
                    return Promise.resolve();
                }

                return this.moveItem(item, destination).then(function (updatedItem) {
                    store.rememberFolder(destination);
                    store.closeMoveDialog();
                    return updatedItem;
                });
            },

            startDragging: function (item) {
                this.dragDrop = {
                    item: item,
                    overPath: '',
                    overKey: ''
                };
            },

            setDragTarget: function (target) {
                if (!this.dragDrop.item) {
                    return;
                }

                this.dragDrop.overPath = target && target.path ? target.path : '';
                this.dragDrop.overKey = target ? this.itemKey(target) : '';
            },

            clearDragging: function () {
                this.dragDrop = {
                    item: null,
                    overPath: '',
                    overKey: ''
                };
            },

            canDropOn: function (target) {
                var item = this.dragDrop.item;

                if (!item || !target || target.type !== 'directory') {
                    return false;
                }

                if (!this.canMove) {
                    return false;
                }

                if (this.sameItem(item, target)) {
                    return false;
                }

                return !(item.type === 'directory' && target.path.indexOf(item.path + '/') === 0);
            },

            prepareDropMove: function (target) {
                var item = this.dragDrop.item;

                if (!this.canDropOn(target)) {
                    this.clearDragging();
                    return;
                }

                this.openMoveDialogForDestination(item, target.path);
                this.clearDragging();
            },

            refreshUsage: function (item) {
                var store = this;

                if (!item || !item.path) {
                    return Promise.resolve(null);
                }

                return window.FileManagerApi.usage(item).then(function (usage) {
                    item.usage = usage;

                    if (store.activeItem && store.activeItem.path === item.path) {
                        store.activeItem.usage = usage;
                    }

                    return usage;
                });
            },

            updateFolderPermissions: function (item, overrides) {
                var store = this;

                if (!store.canMaintenance) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                if (!item || item.type !== 'directory') {
                    return Promise.resolve(null);
                }

                store.saving = true;
                store.error = '';

                return window.FileManagerApi.updateFolderPermissions(item, overrides).then(function (updatedItem) {
                    store.items = store.items.map(function (currentItem) {
                        return store.sameItem(currentItem, updatedItem) ? updatedItem : currentItem;
                    });
                    store.activeItem = updatedItem;

                    return store.load(store.path, 1).then(function () {
                        store.activeItem = updatedItem;
                        return updatedItem;
                    });
                }).catch(function (error) {
                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not update folder permissions.';
                    throw error;
                }).finally(function () {
                    store.saving = false;
                });
            },

            destroy: function (item, force) {
                var store = this;

                if ((force && !store.canForceDelete) || (!force && !store.canDelete)) {
                    store.setPermissionError();
                    return Promise.reject(new Error(store.error));
                }

                store.saving = true;

                return window.FileManagerApi.destroy(item, force).then(function () {
                    return store.load(store.path, 1);
                }).catch(function (error) {
                    var usage = error.response && error.response.data ? error.response.data.usage : null;
                    var count = usage && usage.count ? usage.count : 0;

                    if (error.response && error.response.status === 409 && count > 0) {
                        item.usage = usage;
                        store.activeItem = item;
                        store.deleteDialog = {
                            isOpen: true,
                            item: item,
                            usage: usage,
                            loadingUsage: false
                        };

                        if (!store.canForceDelete) {
                            store.error = 'This item is used and requires force-delete permission.';
                        }
                    }

                    store.error = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'Could not delete the selected item.';
                    throw error;
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
