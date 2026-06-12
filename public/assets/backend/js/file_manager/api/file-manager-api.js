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

    window.FileManagerApi = {
        list: function (path) {
            return window.appAxios.get(window.FileManagerConfig.endpoints.list, {
                params: params({path: path})
            }).then(function (response) {
                return response.data.data;
            });
        },

        uploadPhoto: function (payload) {
            var formData = new FormData();
            formData.append('photo', payload.file);
            formData.append('path', payload.path || '');
            formData.append('name', payload.name || '');
            formData.append('preset', payload.preset || '');

            if (payload.width) {
                formData.append('width', payload.width);
            }

            if (payload.height) {
                formData.append('height', payload.height);
            }

            return window.appAxios.post(window.FileManagerConfig.endpoints.uploadPhoto, formData, {
                headers: {'Content-Type': 'multipart/form-data'}
            }).then(function (response) {
                return response.data.data;
            });
        },

        createFolder: function (path, name) {
            return window.appAxios.post(window.FileManagerConfig.endpoints.folder, {
                path: path || '',
                name: name
            }).then(function (response) {
                return response.data.data;
            });
        },

        destroy: function (item) {
            return window.appAxios.delete(window.FileManagerConfig.endpoints.destroy, {
                data: {
                    path: item.path,
                    type: item.type
                }
            }).then(function (response) {
                return response.data;
            });
        }
    };
})(window);
