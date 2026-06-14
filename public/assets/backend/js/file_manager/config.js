(function (window) {
    'use strict';

    window.FileManagerConfig = {
        endpoints: {
            list: '/dashboard/file-manager',
            tree: '/dashboard/file-manager/tree',
            uploadPhoto: '/dashboard/file-manager/photo',
            folder: '/dashboard/file-manager/folder',
            usage: '/dashboard/file-manager/usage',
            rename: '/dashboard/file-manager/item/rename',
            move: '/dashboard/file-manager/item/move',
            destroy: '/dashboard/file-manager/item',
            thumbnailCache: '/dashboard/file-manager/maintenance/thumbnail-cache',
            importMedia: '/dashboard/file-manager/maintenance/import',
            importHistory: '/dashboard/file-manager/maintenance/imports',
            folderPermissions: '/dashboard/file-manager/folder/permissions'
        },
        permissions: Object.assign({
            read: true,
            upload: true,
            create_folder: true,
            rename: true,
            move: true,
            delete: true,
            force_delete: false,
            track_usage: true,
            forget_usage: true,
            maintenance: false
        }, window.FileManagerPermissions || {}),
        presets: [
            {key: 'free', label: 'Free crop', width: null, height: null},
            {key: 'avatar', label: 'Avatar 512 x 512', width: 512, height: 512},
            {key: 'card', label: 'Card 800 x 600', width: 800, height: 600},
            {key: 'banner', label: 'Banner 1600 x 600', width: 1600, height: 600},
            {key: 'wide', label: 'Wide 1920 x 1080', width: 1920, height: 1080},
            {key: 'thumb', label: 'Thumb 320 x 240', width: 320, height: 240}
        ],
        defaults: {
            accept: 'image/*',
            multiple: false,
            path: ''
        }
    };
})(window);
