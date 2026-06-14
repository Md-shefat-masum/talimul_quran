(function (window) {
    'use strict';

    function params(values) {
        return Object.keys(values || {}).reduce(function (data, key) {
            if (values[key] !== undefined && values[key] !== null && values[key] !== '') {
                data[key] = values[key];
            }

            return data;
        }, {});
    }

    function itemPayload(item) {
        item = item || {};

        return {
            id: item.id || null,
            media_id: item.media_id || (item.type === 'file' ? item.id : null),
            folder_id: item.folder_id || (item.type === 'directory' ? item.id : null),
            path: item.path || '',
            type: item.type
        };
    }

    window.FileManagerApi = {
        list: function (path, options) {
            options = options || {};

            return window.appAxios.get(window.FileManagerConfig.endpoints.list, {
                params: params({
                    path: path,
                    folder_id: options.folderId,
                    page: options.page,
                    per_page: options.perPage,
                    q: options.query
                })
            }).then(function (response) {
                return response.data.data;
            });
        },

        tree: function (parent) {
            parent = parent || {};

            return window.appAxios.get(window.FileManagerConfig.endpoints.tree, {
                params: params({
                    path: parent.path,
                    folder_id: parent.folderId
                })
            })
                .then(function (response) {
                    return response.data.data;
                });
        },

        uploadPhoto: function (payload) {
            var formData = new FormData();
            formData.append('photo', payload.file);
            formData.append('path', payload.path || '');
            formData.append('folder_id', payload.folderId || '');
            formData.append('name', payload.name || '');
            formData.append('preset', payload.preset || '');
            formData.append('conflict_strategy', payload.conflictStrategy || 'rename');

            if (payload.width) {
                formData.append('width', payload.width);
            }

            if (payload.height) {
                formData.append('height', payload.height);
            }

            return window.appAxios.post(window.FileManagerConfig.endpoints.uploadPhoto, formData, {
                headers: {'Content-Type': 'multipart/form-data'},
                onUploadProgress: payload.onProgress || null
            }).then(function (response) {
                return response.data.data;
            });
        },

        createFolder: function (path, name, folderId) {
            return window.appAxios.post(window.FileManagerConfig.endpoints.folder, {
                path: path || '',
                folder_id: folderId || null,
                name: name
            }).then(function (response) {
                return response.data.data;
            });
        },

        usage: function (item) {
            return window.appAxios.get(window.FileManagerConfig.endpoints.usage, {
                params: params(itemPayload(item))
            }).then(function (response) {
                return response.data.data;
            });
        },

        trackUsage: function (items, context) {
            return window.appAxios.post(window.FileManagerConfig.endpoints.usage, Object.assign({}, context, {
                items: items.map(function (item) {
                    return {
                        id: item.id || null,
                        media_id: item.media_id || (item.type === 'file' ? item.id : null),
                        path: item.path,
                        url: item.url || null
                    };
                })
            })).then(function (response) {
                return response.data.data;
            });
        },

        rename: function (item, name) {
            return window.appAxios.patch(window.FileManagerConfig.endpoints.rename, Object.assign(itemPayload(item), {
                name: name
            })).then(function (response) {
                return response.data.data;
            });
        },

        move: function (item, destination) {
            return window.appAxios.patch(window.FileManagerConfig.endpoints.move, Object.assign(itemPayload(item), {
                destination: destination || '',
                destination_folder_id: null
            })).then(function (response) {
                return response.data.data;
            });
        },

        destroy: function (item, force) {
            return window.appAxios.delete(window.FileManagerConfig.endpoints.destroy, {
                data: Object.assign(itemPayload(item), {
                    force: Boolean(force)
                })
            }).then(function (response) {
                return response.data;
            });
        },

        thumbnailCache: function () {
            return window.appAxios.get(window.FileManagerConfig.endpoints.thumbnailCache)
                .then(function (response) {
                    return response.data.data;
                });
        },

        clearThumbnailCache: function () {
            return window.appAxios.delete(window.FileManagerConfig.endpoints.thumbnailCache)
                .then(function (response) {
                    return response.data.data;
                });
        },

        importMedia: function (payload) {
            payload = payload || {};

            return window.appAxios.post(window.FileManagerConfig.endpoints.importMedia, {
                path: payload.path || 'uploads',
                recursive: payload.recursive !== false,
                dry_run: Boolean(payload.dryRun),
                limit: payload.limit || null
            }).then(function (response) {
                return response.data.data;
            });
        },

        importHistory: function (limit) {
            return window.appAxios.get(window.FileManagerConfig.endpoints.importHistory, {
                params: params({limit: limit || 5})
            }).then(function (response) {
                return response.data.data;
            });
        },

        updateFolderPermissions: function (item, overrides) {
            return window.appAxios.patch(window.FileManagerConfig.endpoints.folderPermissions, {
                folder_id: item.folder_id || item.id || null,
                path: item.path || '',
                overrides: overrides || {}
            }).then(function (response) {
                return response.data.data;
            });
        }
    };
})(window);
